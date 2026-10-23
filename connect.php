<?php
/* Deteksi HTTPS. Dipakai untuk cookie Secure dan session.cookie_secure.
 * Penting untuk lokal (http://localhost): bila cookie diberi flag Secure,
 * browser membuangnya -> login terlihat sukses tapi selalu kembali ke login. */
function shenn_is_https() {
    if(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $proto = strtolower(trim(explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'])[0]));
        if($proto === 'https') return true;
    }
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && strtolower((string) $_SERVER['HTTPS']) !== 'off') return true;
    if(isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) return true;
    return false;
}

if(!isset($_SESSION)) {
    /* Di produksi semua nilai ini sudah di-set lewat php_value di .htaccess.
     * Di lokal php_value sering tidak aktif, jadi kita set manual di sini
     * agar perilakunya sama persis di produksi maupun di lokal. */
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_secure', shenn_is_https() ? '1' : '0');
    session_start();
}
date_default_timezone_set('Asia/Jakarta');
ini_set('memory_limit', '1024M');
$date = date('Y-m-d');
$time = date('H:i:s');
$dtme = date('Y-m-d H:i:s');

/* === Local secrets: pindahkan kredensial ke library/config.local.php atau env ===
 * Format library/config.local.php:
 *   <?php return ['db_host'=>'...','db_user'=>'...','db_pass'=>'...','db_name'=>'...','wapi_key'=>'...'];
 * Environment variable: SHENN_DB_HOST, SHENN_DB_USER, SHENN_DB_PASS, SHENN_DB_NAME, SHENN_WAPI_KEY
 * Nilai default di bawah hanya fallback; WAJIB segera dirotasi. */
$shenn_local = [];
$shenn_local_file = __DIR__.'/library/config.local.php';
if(is_file($shenn_local_file)) {
    $shenn_loaded = require $shenn_local_file;
    if(is_array($shenn_loaded)) $shenn_local = $shenn_loaded;
}
function shenn_secret($key, $default) {
    global $shenn_local;
    $env = getenv('SHENN_'.strtoupper($key));
    if($env !== false && $env !== '') return $env;
    if(isset($shenn_local[$key]) && $shenn_local[$key] !== '') return $shenn_local[$key];
    return $default;
}
$aiy = [
    'host' => shenn_secret('db_host','altamura-db.id.domainesia.com'),
    'user' => shenn_secret('db_user','ampangpe_wc-17'), # Username Database
    'pass' => shenn_secret('db_pass','ampangpe_wc-17'), # Password Database
    'name' => shenn_secret('db_name','ampangpe_wc-17')  # Nama Database
];
$call = mysqli_connect($aiy['host'],$aiy['user'],$aiy['pass'],$aiy['name']);
if(!$call) { file_put_contents('shenn.log',"[$date $time] Connection Failed: ".mysqli_connect_error()."\n", FILE_APPEND); require 'page/server-error.php'; }

/* Menentukan root aplikasi.
 * Produksi : DOCUMENT_ROOT = folder project -> dipakai langsung.
 * Lokal    : project sering diletakkan di bawah docroot (mis. C:\xampp\htdocs\shennboku),
 *            sehingga DOCUMENT_ROOT menunjuk ke htdocs, BUKAN ke folder project.
 *            Karena itu fallback-nya adalah folder tempat file ini berada. */
function _DIR_($path,$x = 'php') {
    global $_SERVER; global $call;
    static $DROOT = null;
    if($DROOT === null) {
        $docroot = (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '') ? rtrim($_SERVER['DOCUMENT_ROOT'],"/\\") : '';
        if($docroot !== '' && is_file($docroot.'/connect.php')) {
            $DROOT = $docroot;
        } else {
            $DROOT = __DIR__;
            if(isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '') {
                // dicatat sekali saja untuk debugging instalasi lokal
                @file_put_contents($DROOT.'/shenn.log', "[".date('Y-m-d H:i:s')."] DOCUMENT_ROOT tidak berisi connect.php, fallback ke: $DROOT\n", FILE_APPEND);
            }
        }
        if($call) $call->query("UPDATE conf SET c2 = '".$call->real_escape_string($DROOT)."' WHERE code = 'license'");
    }
    if($x == 'php') return (stristr($path,'.php')) ? "$DROOT/$path" : "$DROOT/$path.php";
    else return "$DROOT/$path";
}

function ShennQuire($f,$x) { for($i = 0; $i <= count($x)-1; $i++) require _DIR_($f.$x[$i]); }

require _DIR_('library/mainfunction'); // This file must be called first!
require _DIR_('library/function/csrf_token');
require _DIR_('library/function/cURL');
require _DIR_('library/function/mailer');
require _DIR_('library/function/SimCardDetector');
require _DIR_('library/customfunction'); // This file must be called last!
require _DIR_('library/ratelimit');
require _Dir_('library/class.wapisender');
require _DIR_('library/database'); // This file must be called last!
require _Dir_('library/LannSistem/customsistem');

$_CONFIG = [
    'title'         => conf('webcfg',1),
    'navbar'        => conf('webcfg',2),
    'description'   => conf('webcfg',3),
    'keyword'       => conf('webcfg',4),
    'banner'        => conf('webcfg',5),
    'icon'          => conf('webcfg',6),
    'hold'          => [
        'Basic' => conf('hold-balance',1),
        'Premium' => conf('hold-balance',2),
        'Admin' => conf('hold-balance',3),
    ],
    'lock'          => check_lock(isset($_SESSION['user']['username']) ? $_SESSION['user']['username'] : ''),
    'mt'            => [
        'web' => conf('webmt',1),
        'api' => conf('webmt',2),
        'bot' => [
            'telegram' => conf('webmt',4),
            'whatsapp' => conf('webmt',3)
        ],
        'trx' => conf('webmt',5),
        'topup' => conf('webmt',6)
    ],
    'reCAPTCHA'     => [
        'site' => conf('webcfg',7),
        'secret' => conf('webcfg',8)
    ],
    'firebase'      => (conf('firebase',8) == 'true') ? 'true' : 'false',
    'license'       => conf('license',1),
    'url'       => conf('license',3),
];

$_META = [
    'robots' => 'index, follow',
    'revisit' => '1 days',
    'bing_site' => 'D00E1C1DD990CCE27CFAB8A295D202C4',
    'google_site' => 'nuqTjnjDZ-4ufOiWTdhnsLKRkE2PIolE0Op-ZUW07cM',
    'google_tagmanager' => 'GTM-TAGCODE',
    'geo_placename' => 'Indonesia',
    'geo_country' => 'Id'
];

$_MAILER = [
    'host' => conf('mailer',1),
    'user' => conf('mailer',2),
    'pass' => conf('mailer',3),
    'from' => conf('mailer',4),
];

$_USER = [
    'theme' => 'dark-layout',
    'require' => [
        'location' => (conf('xtra-fitur',6) == 'true') ? 'true' : 'false',
    ]
];

// ========================= START: Custom Required ========================= //

/* Required: Developer Mode? */
if(conf('xtra-fitur',7) != 'true') error_reporting(0);

/* Required: Firebase Cloud Messaging */
require _DIR_('library/function/FirebaseCM');
$FCM = new FirebaseCM(conf('firebase',1), conf('firebase',3), conf('firebase',4));

/* Required: Atlantic WhatsApp */
require _DIR_('library/function/AtlanticWhatsapp');
$WATL = new WhatsATL(conf('atlantic-cfg',1), conf('atlantic-cfg',2), conf('atlantic-cfg',4));

require _DIR_('library/function/WapiSender');
$WAPI = new WapiSender(shenn_secret('wapi_key','4klds4PyUDZn5fu5of9hhjQKeBBNDHlM'),'mivcvl','https://wapisender.id/api/v5');
// ========================== END: Custom Required ========================== //
 
if(preg_match('#Mozilla/4.05 [fr] (Win98; I)#',$user_agent)
    || preg_match('/Java1.1.4/si',$user_agent)
    || preg_match('/MS FrontPage Express/si',$user_agent)
    || preg_match('/HTTrack/si',$user_agent)
    || preg_match('/IDentity/si',$user_agent)
    || preg_match('/HyperBrowser/si',$user_agent)
    || preg_match('/Lynx/si',$user_agent)
) die('Be a Creative Human Dude<br><br>- Shenn x Safa');

$page_load = explode(' ', microtime());
$page_load = $page_load[1] + $page_load[0];
$page_load_start = $page_load;
