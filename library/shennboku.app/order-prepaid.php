<?php
if(!isset($call)) die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");
if(isset($_POST['order'])) {
    $web_token = filter(base64_decode($_POST['web_token']));
    $tgt_token = filter(base64_decode($_POST['tgt_token']));
    $pin_lock = filter($_POST['pin']);
    
    $check_service = $call->query("SELECT * FROM srv_ppob WHERE code = '$web_token' AND status = 'available'");
    if($check_service->num_rows > 0) {
        $data_srv = $check_service->fetch_assoc();
        $provider = $data_srv['provider'];
        $service = $data_srv['name'];
        $profit = (in_array($data_user['level'],['Admin','Premium'])) ? $data_srv['premium'] : $data_srv['basic'];
        $price = $data_srv['price']+$profit;
        $point = point($price,$profit,'ppob');
        $brand = $data_srv['brand'];
        
        if(in_array($data_srv['type'],['pulsa-reguler','pulsa-transfer','paket-internet','paket-telepon'])) {
            $cek_brand = strtr(strtoupper($SimCard->operator($tgt_token)),['THREE' => 'TRI','SMARTFREN' => 'SMART']);
            if(strtolower($cek_brand) == 'unknown') $validasi_brand = ['result' => false,'msg' => 'Unknown number.'];
            else if(strtolower($cek_brand) == strtolower($brand)) $validasi_brand = ['result' => true];
            else if(strtolower($cek_brand) !== strtolower($brand)) $validasi_brand = ['result' => false,'msg' => 'Incorrect number.'];
            else  $validasi_brand = ['result' => false,'msg' => 'System Error.'];
        } else {
            $validasi_brand = ['result' => true];
        }
        
        $check_provider = $call->query("SELECT * FROM provider WHERE code = '$provider'");
        if($check_provider->num_rows > 0) $data_prv = $check_provider->fetch_assoc();
        
        if($provider == 'KLANN') {
            $ShennORD = $call->query("SELECT id FROM trx_ppob WHERE data LIKE '%$tgt_token%' AND date_cr LIKE '$date%' AND code = '$web_token'")->num_rows;
            $tgt_token = ($ShennORD > 0) ? ($ShennORD + 1).'.'.$tgt_token : $tgt_token;
        }
    }
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if($_CONFIG['mt']['trx'] == 'true') {
        ShennXit(['type' => false,'message' => 'There is a transaction system maintenance, please try again later.']);
    } else if(!$web_token || !$tgt_token) {
        ShennXit(['type' => false,'message' => 'Order Token not detected.']);
    } else if(!$pin_lock) {
        ShennXit(['type' => false,'message' => 'Enter your Security PIN.']);
    } else if(shenn_rl_pin_gate($data_user['username']) == false) {
        ShennXit(['type' => false,'message' => 'Too many incorrect PIN attempts. Please try again in a few minutes.']);
    } else if(check_bcrypt($pin_lock,$data_user['secure']) == false) {
        shenn_rl_pin_fail($data_user['username']);
        ShennXit(['type' => false,'message' => 'Invalid Security PIN.']);
    } else if($check_service->num_rows == 0) {
        ShennXit(['type' => false,'message' => 'Service not found or currently unavailable.']);
    } else if($check_provider->num_rows == 0) {
        ShennXit(['type' => false,'message' => 'Server not found or currently unavailable.']);
    } else if($validasi_brand['result'] == false) {
        ShennXit(['type' => false,'message' => $validasi_brand['msg']]);
    } else if($data_user['balance'] < $price) {
        ShennXit(['type' => false,'message' => 'Your balance is not sufficient to place this order.']);
    } else if($data_user['balance'] < ($price+$_CONFIG['hold'][$data_user['level']])) {
        ShennXit(['type' => false,'message' => 'Your minimum account balance is '.number_format($_CONFIG['hold'][$data_user['level']]).'.']);
    } else if(date_diffs(last_trx($sess_username, 'ppob'), $dtme, 'second') < 6) {
        ShennXit(['type' => false,'message' => 'Transactions are limited, please try again within 6 seconds.']);
    } else {
        $ShennTRX = date('YmdHis').random_number(2);
        if($call->query("SELECT id FROM trx_ppob WHERE wid = '$ShennTRX'")->num_rows > 0) ShennXit(['type' => false,'message' => 'Clashing System, please try again later!']);
        
        $inp = $call->query("INSERT INTO trx_ppob VALUES (null,'$ShennTRX','','$sess_username','$web_token','$service','$tgt_token','','$price','0','$profit','waiting','0','0','WEB,$client_ip','prepaid','$date $time','$date $time','$provider')");
        if($inp == true) $inp1 = $call->query("UPDATE users SET balance = balance-$price WHERE username = '$sess_username'");
        
        if($data_user['balance'] > $price) {
            if(isset($inp1) && $inp1 == false) {
                if($inp == true) $call->query("DELETE FROM trx_ppob WHERE wid = '$ShennTRX'");
                ShennXit(['type' => false,'message' => 'An error occurred with the user data.']);
            } else if($inp == true && $inp1 == true) {
                $req_result = false;
                $req_message = 'Connection not found.';
                require 'order-prepaid-curl.php';
                
                if($req_result == true) {
                    $call->query("INSERT INTO mutation VALUES (null,'$sess_username','-','$price','Prepaid Order :: $ShennTRX','$date $time')");
                    $call->query("INSERT INTO logs VALUES (null,'$sess_username','order','Prepaid Order :: $ShennTRX','primary','$client_ip','$client_iploc','$date $time')");
                    $call->query("UPDATE trx_ppob SET tid = '$req_provid', note = '$req_note', status = '$req_status' WHERE wid = '$ShennTRX'");
                    if(isset($req_lastbl)) $call->query("UPDATE provider SET balance = '$req_lastbl' WHERE code = '$provider'");
                    if($req_status == 'success' && $call->query("SELECT * FROM users WHERE referral = '".$data_user['uplink']."'")->num_rows == 1) {
                        $call->query("UPDATE users SET point = point+$point WHERE referral = '".$data_user['uplink']."'");
                        $call->query("UPDATE trx_ppob SET profit = profit-$point, spoint = '1', point = '$point' WHERE wid = '$ShennTRX'");
                    }
                    //ShennXit(['type' => true,'message' => $req_note]);
                    redirect(0,base_url('detail/transaction/'.$ShennTRX));
                } else {
                    $call->query("INSERT INTO logs VALUES (null,'system','system','[$provider] [WEB] [$ShennTRX] Prepaid: $req_message','primary','$client_ip','$client_iploc','$date $time')");
                    $call->query("UPDATE users SET balance = balance+$price WHERE username = '$sess_username'");
                    $req_msg = (stristr(strtolower($req_message),'saldo') || stristr(strtolower($req_message),'balance')) ? 'Something went wrong, please contact the admin.' : $req_message;
                    if(conf('xtra-fitur',7) == 'true') {
                        $req_msg = $req_message;
                        $call->query("INSERT INTO logs VALUES (null,'$sess_username','order','Prepaid Order :: $ShennTRX','primary','$client_ip','$client_iploc','$date $time')");
                        $call->query("INSERT INTO mutation VALUES (null,'$sess_username','-','$price','Prepaid Order :: $ShennTRX','$date $time'), (null,'$sess_username','+','$price','Prepaid Refunds :: $ShennTRX','$date $time')");
                        $call->query("UPDATE trx_ppob SET note = '$req_msg', status = 'error', price = '0', profit = '0', refund = '1' WHERE wid = '$ShennTRX'");
                    } else {
                        $call->query("DELETE FROM trx_ppob WHERE wid = '$ShennTRX'");
                    }
                    ShennXit(['type' => false,'message' => $req_msg]);
                }
            } else {
                if($inp == true) $call->query("DELETE FROM trx_ppob WHERE wid = '$ShennTRX'");
                if($inp1 == true) $call->query("UPDATE users SET balance = balance+$price WHERE username = '$sess_username'");
                ShennXit(['type' => false,'message' => 'An error occurred in the transaction data.']);
            }
        } else {
            if($inp == true) $call->query("DELETE FROM trx_ppob WHERE wid = '$ShennTRX'");
            if($inp1 == true) $call->query("UPDATE users SET balance = balance+$price WHERE username = '$sess_username'");
            ShennXit(['type' => false,'message' => 'Your balance is low.']);
        }
    }
}