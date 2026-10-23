<?php
require __DIR__.'/_guard.php';
set_time_limit(240);
require '../../connect.php';
require 'helper.php';

$getkey = isset($_GET['noresult']) ? false : true;
$showpw = isset($_SESSION['user']['level']) ? $_SESSION['user']['level'] : 'invalid';
$stdate = date('Y-m-d', strtotime('-3 Months', strtotime($date)));
$search = $call->query("SELECT * FROM trx_ppob WHERE status IN ('waiting','') AND provider != 'X' AND DATE(date_cr) BETWEEN '$stdate' AND '$date' ORDER BY rand() LIMIT 20");
if($search->num_rows == 0) {
    print '<font color="red"><pre>Order Waiting not found</pre></font>';
} else {
    while($row = $search->fetch_assoc()) {
        $wid = $row['wid'];
        $tid = $row['tid'];
        $user = $row['user'];
        $server = $row['provider'];
        $ostatus = $row['status'];
        $prov = $call->query("SELECT * FROM provider WHERE code = '$server'")->fetch_assoc();
        $usr_data = $call->query("SELECT * FROM users WHERE username = '$user'")->fetch_assoc();
        
        if($server == 'ATL') {
            if($row['trxtype'] == 'prepaid') {
                $try = $curl->connectPost($prov['link'].'transaksi/status',['api_key' => $prov['apikey'],'id' => $tid,'type' => 'prabayar']);
                $data['success'] = isset($try['status']) ? $try['status'] : false;
                $data['status'] = $data['success'] == true ? $try['data']['status'] : 'waiting';
                $data['errors'] = $data['success'] == false ? isset($try['message']) ? $try['message'] : 'Connection Failed!' : '';
                $data['note'] = $data['success'] == true ? $try['data']['sn'] : '';
            } else {
                $data = [
                    'success' => false,
                    'status' => 'error',
                    'errors' => 'Transaction not supported / still in the making stage.',
                    'note' => ''
                ];
            }
        } else {
            $data = [
                'success' => false,
                'status' => 'error',
                'errors' => 'Provider not supported / still in the making stage.',
                'note' => ''
            ];
        }
        
        if($data['success'] == true) {
            $status = $helper->status($data['status']);
            $note = filter($data['note']);
            $point = point($row['price'],$row['profit'],'ppob');
            
            if($call->query("UPDATE trx_ppob SET status = '$status', note = '$note', date_up = '$date $time' WHERE wid = '$wid'") == true) {
                if($status == 'success' && $row['spoint'] == '0' && $call->query("SELECT * FROM users WHERE referral = '{$usr_data['uplink']}'")->num_rows == 1) {
                    $call->query("UPDATE users SET point = point+$point WHERE referral = '{$usr_data['uplink']}'");
                    $call->query("UPDATE trx_ppob SET profit = profit-$point, spoint = '1', point = '$point' WHERE wid = '$wid'");
                }
                                             
                if(in_array($status,['success','error'])) {
                    $msg = "Hallo ".$user." Update Status Pesanan :
Order Id : ".$wid."
Layanan : ".$row['name']."
Target : ".$row['data']."
Harga : ".currency($row['price'])."
SN/Ket : ".$note."
Status : ".$status."

Terimakasih Sudah Order.Lanjut Terus";
                    lann_sender_message($usr_data['phone'],$msg);
                }           
                             
                if($getkey == true) {
                    print '<font color="green"><pre>[+] '.$wid.' {Update Success}</pre></font>';
                    if($showpw == 'Admin') {
                        print '<font color="green"><pre>User: '.$row['user'].'</pre></font>';
                        print '<font color="green"><pre>Prov: '.$server.'</pre></font>';
                        print '<font color="green"><pre>Note: '.$note.'</pre></font>';
                        print '<font color="green"><pre>Status: '.$ostatus.' -> '.$status.'</pre></font>';
                    }
                    print '<hr>';
                }
            } else {
                if($getkey == true) {
                    print '<font color="red"><pre>[!] '.$wid.' {Update Failed}</pre></font>';
                    if($showpw == 'Admin') {
                        print '<font color="red"><pre>User: '.$row['user'].'</pre></font>';
                        print '<font color="red"><pre>Prov: '.$server.'</pre></font>';
                        print '<font color="red"><pre>Note: '.$note.'</pre></font>';
                        print '<font color="red"><pre>Status: '.$status.'</pre></font>';
                    }
                    print '<hr>';
                }
            }
        } else {
            if($getkey == true) {
                print '<font color="red"><pre>[!] '.$wid.' {'.$data['errors'].'}</pre></font>';
                if($showpw == 'Admin') {
                    print '<font color="red"><pre>User: '.$row['user'].'</pre></font>';
                    print '<font color="red"><pre>Prov: '.$server.'</pre></font>';
                }
                print '<hr>';
            }
        }
    }
}
squery("UPDATE conf SET c5 = '$date $time' WHERE code = 'status-refund-access'");