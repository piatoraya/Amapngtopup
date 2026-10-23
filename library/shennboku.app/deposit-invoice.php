<?php
if(!isset($call)) die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");
$InvoiceCode = isset($_GET['qq']) ? filter($_GET['qq']) : '';
$search = $call->query("SELECT * FROM deposit WHERE wid = '$InvoiceCode' AND user = '$sess_username' AND payment NOT IN ('transfer','voucher')");
if($search->num_rows == false) redirect(0,base_url('deposit/history'));
$rowvoice = $search->fetch_assoc();

$payname = $call->query("SELECT * FROM deposit_payment WHERE code = '".$rowvoice['payment']."' ORDER BY name ASC LIMIT 1");
$payname = ($payname->num_rows == 1) ? $payname->fetch_assoc()['name'] : 'Unknown';
$paytotal = $rowvoice['amount']+$rowvoice['fee']+$rowvoice['uniq'];

function set_status_for_atlantic($x) {
    $y = strtolower($x);
    if(in_array($y,['cancel','error','cancelled','expired'])) $stt = 'cancelled';
    if(in_array($y,['pending','waiting','unpaid'])) $stt = 'unpaid';
    if(in_array($y,['success','sukses','dibayar','paid'])) $stt = 'paid';
    return (!isset($stt)) ? 'unpaid' : $stt;
}

if(isset($_POST['instant'])) {
    $web_token = filter(base64_decode($_POST['web_token']));
    $deps = $call->query("SELECT * FROM deposit WHERE id = '$web_token' AND user = '$sess_username' AND status = 'process' AND payment NOT IN('transfer','voucher')")->fetch_assoc();

    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if(!is_array($deps)) {
        ShennXit(['type' => false,'message' => 'Invoice not found.']);
    }

    $provider = $call->query("SELECT * FROM provider WHERE code = 'ATL'")->fetch_assoc();
    $connect = $curl->connectPost($provider['link'].'deposit/instant',['api_key' => $provider['apikey'],'id' => base64_decode($deps['sender']),'action' => 'true']);
    if(isset($connect['status'])) {
        if($connect['status'] == true) {
            $nominal = $connect['data']['nominal'];
            $penanganan = $connect['data']['penanganan'];
            $total_diterima = $nominal-$deps['fee']-$penanganan;
            $status = set_status_for_atlantic($connect['data']['status']);
            $total_fee = $deps['fee']+$penanganan;
            
            $query_update = $call->query("UPDATE deposit SET status = 'paid', note = '', sender = '', fee = '$total_fee', amount = '$total_diterima' WHERE id = '".$deps['id']."'");
            $query_update = $call->query("UPDATE users SET balance = balance+$total_diterima WHERE username = '".$deps['user']."'");
            
            if($query_update == true) {
                $call->query("INSERT INTO mutation VALUES (null,'".$deps['user']."','+','$total_diterima','Deposit :: {$deps['wid']}','$date $time')");
                
                $search_user = $call->query("SELECT * FROM users WHERE username = '".$deps['user']."'")->fetch_assoc();                
                $msg = "Hallo ".$rowvoice['user']."\nDeposit Sebesar Rp ".currency($total_diterima)." dari ".strtoupper($rowvoice['method'])." sudah berhasil ditambahkan ke akun AMPANGPEDIA Anda.";            
                
                lann_sender_message($search_user['phone'],$msg);
                ShennXit(['type' => true,'message' => 'Berhasil melalukan Cair Instant']);
            } else {
                ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
            }                       
        } else {
            ShennXit(['type' => false,'message' => $try['message']]);
        }
    } else {
        ShennXit(['type' => false,'message' => 'Connection Failed!']);
    }
}

if(isset($_POST['cancel'])) {
    $web_token = filter(base64_decode($_POST['web_token']));
    $post_password = filter($_POST['password']);
    
    $check_pin = check_bcrypt($post_password,$data_user['secure']);
    $check_password = ($check_pin == false) ? check_bcrypt($post_password,$data_user['password']) : $check_pin;
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!$web_token || !$post_password) {
        ShennXit(['type' => false,'message' => 'There is still a blank form.']);
    } else if($call->query("SELECT wid FROM deposit WHERE wid = '$web_token' AND status = 'unpaid'")->num_rows == false) {
        ShennXit(['type' => false,'message' => 'Invoice not found.']);
    } else if(shenn_rl_pin_gate($data_user['username'], 5, 15) == false) {
        ShennXit(['type' => false,'message' => 'Too many incorrect attempts. Please try again in a few minutes.']);
    } else if($check_password == false) {
        shenn_rl_pin_fail($data_user['username']);
        ShennXit(['type' => false,'message' => 'Invalid Security PIN or Password.']);
    } else {
        $search_deposit = $call->query("SELECT * FROM deposit WHERE wid = '$web_token'");
        if($search_deposit->num_rows == 1) $row_deposit = $search_deposit->fetch_assoc();
       
        $search_metode = $call->query("SELECT * FROM deposit_method WHERE method = '".$row_deposit['payment']."'");
        if($search_metode->num_rows == 1) $row_metode = $search_metode->fetch_assoc();
        
        if($row_metode['auto'] == 1) {        
            $search_prov = $call->query("SELECT * FROM provider WHERE code = 'ATL'");
            if($search_prov->num_rows == 1) {
                $data_prov = $search_prov->fetch_assoc();
            
                $params = [
                    'api_key' => $data_prov['apikey'],
                    'id' => $row_deposit['sender']
                ]; $try = $curl->connectPost($data_prov['link'].'deposit/cancel',$params);
                
                if(isset($try['status'])) {
                    if($try['status'] == true) {
                        $lann_status = set_status_for_atlantic($try['data']['status']);
                        if($call->query("UPDATE deposit SET status = '$stt', sender = '', note = 'Gagal melakukan pembayaran' WHERE wid = '$web_token' AND status = 'unpaid'") == true) {
                            ShennXit(['type' => true,'message' => 'Deposit request canceled successfully.']);
                        } else {
                            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
                        }
                    } else {
                        ShennXit(['type' => false,'message' => $try['message']]);
                    }
                } else {
                    ShennXit(['type' => false,'message' => 'Connection Failed!']);
                }
            } else {
                ShennXit(['type' => false,'message' => 'Server not found!']);
            }                     
        } else {
            if($call->query("UPDATE deposit SET status = 'cancelled', note = 'Gagal melakukan pembayaran' WHERE wid = '$web_token' AND status = 'unpaid'") == true) {
                ShennXit(['type' => true,'message' => 'Deposit request canceled successfully.']);
            } else {
                ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
            }
        }
    }
}