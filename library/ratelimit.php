<?php
if (defined('SHENN_RATELIMIT_H')) return;
define('SHENN_RATELIMIT_H', true);

/**
 * Rate-limit / anti-brute-force (login, PIN, API) tanpa mengubah skema database.
 *
 * Semua baris kegagalan dicatat ke tabel `logs` yang sudah ada, memakai nilai
 * `type` khusus supaya bisa dihitung kembali:
 *      'login_fail' | 'pin_fail' | 'api_fail'
 * (nilai type lama 'login'/'order'/'register'/'logout'/'system' tetap tidak tersentuh)
 *
 * Dipakai hanya kolom yang terbukti ada (lihat library/ajax/table/activity.php):
 *      id, user, type, text, ip, loc, date
 * Karena itu pencarian hitungan TIDAK menyentuh kolom ke-5 (status).
 *
 * Prinsip: kalau query gagal -> fail-open (protection dilewati, situs tetap jalan).
 *
 * CATATAN PERFORMA (disarankan, jalankan SEKALI di phpMyAdmin):
 *   Tabel `logs` tidak punya index untuk kolom type/date, dan tidak pernah
 *   di-truncate oleh cron mana pun, jadi lama-kelamaan COUNT() bisa melambat.
 *   Tambahkan index sekali saja:
 *       ALTER TABLE `logs` ADD INDEX `idx_rl` (`type`,`date`,`ip`);
 *   Tanpa index tetap berjalan benar, hanya lebih berat saat baris sudah jutaan.
 */

/* ---------- panjang aman untuk kolom logs.user ---------- */
function shenn_rl_key($ident) {
    $ident = (string) $ident;
    if ($ident === '') return '-';
    // Jaga-jaga bila kolom user sempit (varchar(20)): pakai prefix + hash singkat
    return (strlen($ident) > 20) ? 'x'.substr(md5($ident), 0, 19) : $ident;
}

/* ---------- jumlah kegagalan dalam N menit terakhir ---------- */
function shenn_rl_count($type, $where = '', $window_min = 10) {
    global $call;
    $since = date('Y-m-d H:i:s', time() - (max(1, (int) $window_min) * 60));
    $sql = "SELECT COUNT(id) AS c FROM logs WHERE type = '".filter($type)."' AND date >= '$since'";
    if ($where !== '') $sql .= " AND ($where)";
    $q = $call->query($sql);
    if ($q === false || $q->num_rows == 0) return 0;          // gagal query -> fail-open
    $row = $q->fetch_assoc();
    return isset($row['c']) ? (int) $row['c'] : 0;
}

/* ---------- catat satu kegagalan ---------- */
function shenn_rl_log($ident, $type, $text, $ip = null) {
    global $call, $client_ip, $client_iploc, $date, $time, $dtme;
    $ip = ($ip === null) ? (isset($client_ip) ? $client_ip : '-') : $ip;
    $loc = (isset($client_iploc) && $client_iploc !== '') ? $client_iploc : '-';
    $when = (isset($dtme) && $dtme !== '') ? $dtme : "$date $time";
    $u  = shenn_rl_key($ident);
    // Nilai ke-5 memakai 'primary' karena hanya 'success'/'primary' yang terbukti valid.
    $call->query("INSERT INTO logs VALUES (null, '".filter($u)."', '".filter($type)."', '".filter($text)."', 'primary', '".filter($ip)."', '".filter($loc)."', '".filter($when)."')");
}

/* ============================ LOGIN ============================ */
function shenn_rl_login_check($username, $ip, $max_user = 5, $max_ip = 20, $window_min = 10) {
    $by_user = shenn_rl_count('login_fail', "user = '".filter(shenn_rl_key($username))."' AND ip = '".filter($ip)."'", $window_min);
    if ($by_user >= $max_user) {
        return ['result' => false, 'message' => 'Too many failed login attempts. Please try again in a few minutes.'];
    }
    $by_ip = shenn_rl_count('login_fail', "ip = '".filter($ip)."'", $window_min);
    if ($by_ip >= $max_ip) {
        return ['result' => false, 'message' => 'Too many failed login attempts from your network. Please try again later.'];
    }
    return ['result' => true, 'message' => ''];
}

function shenn_rl_login_fail($username, $ip) {
    shenn_rl_log($username, 'login_fail', 'Failed login attempt', $ip);
}

function shenn_rl_login_ok($username, $ip) {
    global $call;
    $u = filter(shenn_rl_key($username));
    $i = filter($ip);
    $call->query("DELETE FROM logs WHERE type = 'login_fail' AND user = '$u' AND ip = '$i' LIMIT 20");
}

/* bool gate -> true = masih boleh mencoba */
function shenn_rl_login_gate($username, $ip, $max_user = 5, $max_ip = 20, $window_min = 10) {
    return shenn_rl_login_check($username, $ip, $max_user, $max_ip, $window_min)['result'];
}

/* ===================== SECURITY PIN ===================== */
function shenn_rl_pin_check($username, $max = 5, $window_min = 15) {
    $c = shenn_rl_count('pin_fail', "user = '".filter(shenn_rl_key($username))."'", $window_min);
    if ($c >= $max) {
        return ['result' => false, 'message' => 'Too many incorrect attempts. Please try again in a few minutes.'];
    }
    return ['result' => true, 'message' => ''];
}

function shenn_rl_pin_fail($username) {
    shenn_rl_log($username, 'pin_fail', 'Failed security PIN/password attempt');
}

function shenn_rl_pin_ok($username) {
    global $call;
    $u = filter(shenn_rl_key($username));
    $call->query("DELETE FROM logs WHERE type = 'pin_fail' AND user = '$u' LIMIT 20");
}

function shenn_rl_pin_gate($username, $max = 5, $window_min = 15) {
    return shenn_rl_pin_check($username, $max, $window_min)['result'];
}

/* ========================= API ========================= */
function shenn_rl_api_check($ident, $max = 30, $window_min = 5) {
    $c = shenn_rl_count('api_fail', "user = '".filter(shenn_rl_key($ident))."'", $window_min);
    if ($c >= $max) {
        return ['result' => false, 'message' => 'Too many failed API requests. Please try again later.'];
    }
    return ['result' => true, 'message' => ''];
}

function shenn_rl_api_fail($ident) {
    shenn_rl_log($ident, 'api_fail', 'Failed API key/signature check');
}
