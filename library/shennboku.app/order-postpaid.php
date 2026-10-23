<?php
if(!isset($call)) die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");
if(isset($_POST['order'])) {
    $web_token = filter(base64_decode($_POST['web_token']));
    $tgt_token = filter(base64_decode($_POST['tgt_token']));
    $pin_lock = filter($_POST['pin']);
    $json_token = json_decode(base64_decode($_POST['json_token']), true);
    
    if(isset($json_token[0]) && isset($json_token[1]) && isset($json_token[2])) {
        $json_token_bills = filter($json_token[0]);
        $json_token_name = filter($json_token[1]);
        $json_token_no = filter($json_token[2]);
    } else {
        ShennXit(['type' => false,'message' => 'Data transmission error, please try again later.']);
    }
    
    $check_service = $call->query("SELECT * FROM srv_ppob WHERE code = '$web_token' AND status = 'available'");
    if($check_service->num_rows > 0) {
        $data_srv = $check_service->fetch_assoc();
        $provider = $data_srv['provider'];
        $service = $data_srv['name'];
        $profit = (in_array($data_user['level'],['Admin','Premium'])) ? $data_srv['premium'] : $data_srv['basic'];
        $price = $data_srv['price']+$profit;
        $point = point($price,$profit,'ppob');
        $brand = $data_srv['brand'];
        
        $check_provider = $call->query("SELECT * FROM provider WHERE code = '$provider'");
        if($check_provider->num_rows > 0) $data_prv = $check_provider->fetch_assoc();
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
    } else if($data_user['balance'] < $price) {
        ShennXit(['type' => false,'message' => 'Your balance is not sufficient to place this order.']);
    } else if($data_user['balance'] < ($price+$_CONFIG['hold'][$data_user['level']])) {
        ShennXit(['type' => false,'message' => 'Your minimum account balance is '.number_format($_CONFIG['hold'][$data_user['level']]).'.']);
    } else if((strtotime("$date $time") - strtotime($call->query("SELECT date_cr FROM trx_ppob WHERE user = '$sess_username' ORDER BY id DESC LIMIT 1")->fetch_assoc()['date_cr'])) < 6) {
        ShennXit(['type' => false,'message' => 'Transactions are limited, please try again within 6 seconds.']);
    } else {
        $ShennTRX = date('YmdHis').random_number(2);
        $out_res = false;
        $out_msg = 'Connection not Found';
        require 'order-postpaid-invcurl.php';
        
        if($out_res == false) {
            ShennXit(['type' => false,'message' => $out_msg]);
        } else if($out_res == true) {
            $price = $price+$out_bills;
            
            if($data_user['balance'] < $price) {
                ShennXit(['type' => false,'message' => 'Your balance is not sufficient to place this order.']);
            } else if($data_user['balance'] < ($price+$_CONFIG['hold'][$data_user['level']])) {
                ShennXit(['type' => false,'message' => 'Your minimum account balance is '.number_format($_CONFIG['hold'][$data_user['level']]).'.']);
            } else if($out_bills <> $json_token_bills) {
                ShennXit(['type' => false,'message' => 'Customer Bills not match.']);
            } else if($out_name <> $json_token_name) {
                ShennXit(['type' => false,'message' => 'Customer Name not match.']);
            } else if($out_no <> $json_token_no) {
                ShennXit(['type' => false,'message' => 'Customer Number not match.']);
            } else if($call->query("SELECT id FROM trx_ppob WHERE wid = '$ShennTRX'")->num_rows > 0) {
                ShennXit(['type' => false,'message' => 'Clashing System, please try again later!']);
            } else {
                $inp = $call->query("INSERT INTO trx_ppob VALUES (null,'$ShennTRX','','$sess_username','$web_token','$service','$tgt_token','','$price','0','$profit','waiting','0','0','WEB,$client_ip','postpaid','$date $time','$date $time','$provider')");
                if($inp == true) $inp1 = $call->query("UPDATE users SET balance = balance-$price WHERE username = '$sess_username'");
                
                if($data_user['balance'] > $price) {
                    if(isset($inp1) && $inp1 == false) {
                        if($inp == true) $call->query("DELETE FROM trx_ppob WHERE wid = '$ShennTRX'");
                        ShennXit(['type' => false,'message' => 'An error occurred with the user data.']);
                    } else if($inp == true && $inp1 == true) {
                        $req_result = false;
                        $req_message = 'Connection not found.';
                        require 'order-postpaid-paycurl.php';
                        
                        if($req_result == true) {
                            $call->query("INSERT INTO mutation VALUES (null,'$sess_username','-','$price','Postpaid Order :: $ShennTRX','$date $time')");
                            $call->query("INSERT INTO logs VALUES (null,'$sess_username','order','Postpaid Order :: $ShennTRX','primary','$client_ip','$client_iploc','$date $time')");
                            $call->query("UPDATE trx_ppob SET tid = '$req_provid', note = '$req_note', status = '$req_status' WHERE wid = '$ShennTRX'");
                            if(isset($req_lastbl)) $call->query("UPDATE provider SET balance = '$req_lastbl' WHERE code = '$provider'");
                            if($req_status == 'success' && $call->query("SELECT * FROM users WHERE referral = '".$data_user['uplink']."'")->num_rows == 1) {
                                $call->query("UPDATE users SET point = point+$point WHERE referral = '".$data_user['uplink']."'");
                                $call->query("UPDATE trx_ppob SET profit = profit-$point, spoint = '1', point = '$point' WHERE wid = '$ShennTRX'");
                            }
                            ShennXit(['type' => true,'message' => $req_note]);
                        } else {
                            $call->query("INSERT INTO logs VALUES (null,'system','system','[$provider] [WEB] [$ShennTRX] Postpaid: $req_message','primary','$client_ip','$client_iploc','$date $time')");
                            $call->query("UPDATE users SET balance = balance+$price WHERE username = '$sess_username'");
                            $req_msg = (stristr(strtolower($req_message),'saldo') || stristr(strtolower($req_message),'balance')) ? 'Something went wrong, please contact the admin.' : $req_message;
                            if(conf('xtra-fitur',7) == 'true') {
                                $req_msg = $req_message;
                                $call->query("INSERT INTO logs VALUES (null,'$sess_username','order','Postpaid Order :: $ShennTRX','primary','$client_ip','$client_iploc','$date $time')");
                                $call->query("INSERT INTO mutation VALUES (null,'$sess_username','-','$price','Postpaid Order :: $ShennTRX','$date $time'), (null,'$sess_username','+','$price','Postpaid Refunds :: $ShennTRX','$date $time')");
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
        } else {
            ShennXit(['type' => false,'message' => 'Failed to check your postpaid.']);
        }
    }
}