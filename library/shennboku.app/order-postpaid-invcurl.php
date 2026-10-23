<?php
if(!isset($call) && !isset($provider) && !isset($data_prv))
    die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");
    
if($provider == 'ATL') {
    $lann_prov = $call->query("SELECT * FROM provider WHERE code = '$provider'")->fetch_assoc();
    $try_check = $curl->connectPost($lann_prov['link'].'transaksi/tagihan',['api_key' => $lann_prov['apikey'],'code' => $web_token,'reff_id' => $ShennTRX,'customer_no' => $tgt_token]);
    file_put_contents('x.json', json_encode($try_check, JSON_PRETTY_PRINT));
    if($try_check['status'] == true) {
        $out_bills = $try_check['data']['total_tagihan'];
        $out_name = $try_check['data']['nama_pelanggan'];
        $out_res = true;
        $out_no = $tgt_token;
    } else {
        $out_msg = (isset($try_check['message'])) ? $try_check['message'] : 'Connection Failed...';
    }        
}