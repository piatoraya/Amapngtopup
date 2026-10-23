<?php
require __DIR__.'/_guard.php';
set_time_limit(240);
require '../../connect.php';

function font_class($t,$msg) {
    if($t == false) {
        return '<font color="red"><pre>[!] '.$msg.'</pre></font>';
    } else {
        return '<font color="green"><pre>'.$msg.'</pre></font>';
    }
}

function set_status_for_atlantic($x) {
    $y = strtolower($x);
    if(in_array($y,['cancel','error','cancelled','expired'])) $stt = 'cancelled';
    if(in_array($y,['pending','waiting','unpaid'])) $stt = 'unpaid';
    if(in_array($y,['success','sukses','dibayar','paid'])) $stt = 'paid';
    if(in_array($y,['process','processing','proses','in progress'])) $stt = 'process';
    return (!isset($stt)) ? 'unpaid' : $stt;
}

$search = $call->query("SELECT * FROM deposit WHERE status IN('unpaid','process')");
if($search->num_rows > 0) {
    while($row = $search->fetch_assoc()) {
        $id = $row['id'];
        $wid = $row['wid'];
        $user = $row['user'];
        $sender = $row['sender'];
        $amount = $row['amount']+$row['uniq'];
        
        $payment = $row['payment'];
        $see_metode = $call->query("SELECT * FROM deposit_method WHERE method = '$payment'");
        if($see_metode->num_rows == 1) {
            $data_metode = $see_metode->fetch_assoc();
            $metode_auto = $data_metode['auto'];
            if($metode_auto == 1) {
                if($sender == '') print font_class(false,$wid.' {kesalahan sistem}');
                $see_provider = $call->query("SELECT * FROM provider WHERE code = 'ATL'");
                if($see_provider->num_rows == 1) {
                    $row_provider = $see_provider->fetch_assoc();
                    $params = ['api_key' => $row_provider['apikey'],'id' => $sender];
                    $try = $curl->connectPost($row_provider['link'].'deposit/status',$params);
                    if(isset($try['status'])) {
                        if($try['status'] == true) {
                            $get_status = set_status_for_atlantic($try['data']['status']);
                            if($call->query("UPDATE deposit SET status = '$get_status' WHERE id = '$id'") == true) {
                                print font_class(true,$wid.' {'.$get_status.'}');                                                           
                                if($get_status == 'paid') { // Paid
                                    $upd = $call->query("INSERT INTO mutation VALUES (null,'$user','+','$amount','Deposit :: $wid','$date $time')");                                
                                    if($upd == true) {
                                        $call->query("UPDATE users SET balance = balance+$amount WHERE username = '$user'");
                                    }
                                }
                            }
                        } else {
                            print font_class(false,$try['message']);
                        }
                    } else {
                        print font_class(false,'Connection Failed!');
                    }
                    
                }
            }
        } else {
            print font_class(false,'Metode not found!');
        }      
    }
} else {
    print font_class(false,'Deposit Unpaid not found!');
}