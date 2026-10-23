<?php
if(!isset($call) && !isset($provider) && !isset($data_prv))
    die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");
    
if($provider == 'DIGI') {
    $try = $DIGI->PayBill($web_token,$tgt_token,$ShennTRX);
    if($try['result'] == true) {
        $req_result = true;
        $req_provid = $try['data']['trxid'];
        $req_status = $try['data']['status'];
        $req_lastbl = $try['data']['balance'];
        $req_note = ($try['message'] == '') ? 'Your order will be processed immediately.' : $try['message'];
    } else {
        $req_message = isset($try['message']) ? $try['message'] : 'Connection Failed!';
    }
} else if($provider == 'ATL') {
    $try = $curl->connectPost($data_prv['link'].'transaksi/tagihan/bayar',['api_key' => $data_prv['apikey'],'reff_id' => $ShennTRX,'code' => $web_token,'customer_no' => $tgt_token]);
    //file_put_contents('atlatic-pascabayar.json', json_encode($try, JSON_PRETTY_PRINT));
    if($try['status'] == true) {
        $req_result = true;
        $req_provid = $try['data']['reff_id'];
        $req_status = $try['data']['status'];
        $req_note = ($try['message'] == '') ? 'Your order will be processed immediately.' : $try['message'];
    } else {
        $req_message = isset($try['message']) ? $try['message'] : 'Connection Failed!';
    }
}