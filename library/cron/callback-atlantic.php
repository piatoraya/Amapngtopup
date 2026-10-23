<?php
/* ShennBoku - Atlantic payment callback (webhook)
 * ------------------------------------------------------------------
 * Perbaikan keamanan:
 *  - Semua input dari body di-escape sebelum masuk query (sebelumnya rawan SQLi).
 *  - Payload diverifikasi harus cocok dengan transaksi yang tersimpan di DB
 *    (wid + code + target), sehingga order orang lain tidak bisa diubah sembarangan.
 *  - Opsional: bila admin mengisi conf('atlantic-cfg', 5) maka akses web wajib ?key=<nilai>.
 *  - File debug tidak lagi ditulis ke webroot.
 */
require '../../connect.php';
require 'helper.php';

$http_req = json_decode(file_get_contents('php://input'), true);
if(!is_array($http_req) || !isset($http_req['data']) || !is_array($http_req['data'])) {
    http_response_code(400);
    die();
}

/* Opsional: shared-secret (isi kolom ke-5 atlantic-cfg di panel admin bila provider mendukung custom callback URL) */
$webhook_key = conf('atlantic-cfg', 5);
if(!empty($webhook_key)) {
    $incoming = isset($_GET['key']) ? (string) $_GET['key'] : '';
    if(!hash_equals((string) $webhook_key, $incoming)) {
        http_response_code(403);
        die();
    }
}

/* Debug ditulis ke folder sementara, bukan webroot */
file_put_contents(sys_get_temp_dir().'/lann-callback-result.json', json_encode($http_req, JSON_PRETTY_PRINT));

$cb_event  = isset($http_req['event']) ? $http_req['event'] : '';
$cb_status = isset($http_req['status']) ? $http_req['status'] : '';

if($cb_event == 'transaksi') {
    if(in_array($cb_status, ['success','cancel','failed'])) {
        $data      = $http_req['data'];
        $reff_id   = isset($data['reff_id']) ? filter($data['reff_id']) : '';
        $note      = isset($data['sn']) ? filter($data['sn']) : '';
        $cb_code   = isset($data['code']) ? (string) $data['code'] : '';
        $cb_target = isset($data['target']) ? (string) $data['target'] : '';
        $status    = $helper->status($cb_status);

        if(!preg_match('/^[A-Za-z0-9\-]{1,64}$/', $reff_id)) die();

        $search = $call->query("SELECT * FROM trx_ppob WHERE wid = '$reff_id' AND status IN('waiting','')");
        if($search->num_rows == 1) {
            $row = $search->fetch_assoc();

            /* Verifikasi identitas transaksi:
             * - Cara utama: cocokkan ID internal provider (kolom tid) dengan data.id dari callback.
             * - Fallback (bila tid belum tersimpan): wajib cocok code DAN target pesanan. */
            $cb_id = isset($data['id']) ? (string) $data['id'] : '';
            if($row['tid'] !== '' && $cb_id !== '') {
                if(!hash_equals((string) $row['tid'], $cb_id)) die();
            } else {
                $code_ok = ($cb_code == '') ? false : (strcasecmp($cb_code, $row['code']) == 0);
                $tgt_ok  = ($cb_target == '') ? false : (($row['provider'] == 'KLANN') ? true : (strcasecmp($cb_target, $row['data']) == 0));
                if(!$code_ok || !$tgt_ok) die();
            }

            $user = $row['user'];
            if($row['provider'] == 'ATL') {
                $phone  = $call->query("SELECT * FROM users WHERE username = '$user'")->fetch_assoc()['phone'];
                $uplink = $call->query("SELECT * FROM users WHERE username = '$user'")->fetch_assoc()['uplink'];
                $point = point($row['price'],$row['profit'],'ppob');
                if($call->query("UPDATE trx_ppob SET note = '$note', status = '$status', date_up = '$date $time' WHERE wid = '$reff_id'") == true) {
                    if($status == 'success' && $row['spoint'] == '0' && $call->query("SELECT * FROM users WHERE referral = '$uplink'")->num_rows == 1) {
                        $call->query("UPDATE users SET point = point+$point WHERE referral = '$uplink'");
                        $call->query("UPDATE trx_ppob SET profit = profit-$point, spoint = '1', point = '$point' WHERE wid = '$reff_id'");
                    }

                    if(in_array($status,['success','error'])) {
                        $msg = "Hallo ".$user." Update Status Pesanan :
Order Id : ".$row['wid']."
Layanan : ".$row['name']."
Target : ".$row['data']."
Harga : ".currency($row['price'])."
Info : ".$note."
Status : ".$status."

Terimakasih Sudah Order.Lanjut Terus";
                        lann_sender_message($phone,$msg);
                    }
                }
            }
        }
    }
}
