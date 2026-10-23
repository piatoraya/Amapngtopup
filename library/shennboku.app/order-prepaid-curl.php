<?php
if(!isset($call) && !isset($provider) && !isset($data_prv))
    die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");
    
if($provider == 'ATL') {
    $try = $curl->connectPost($data_prv['link'].'transaksi/create',['api_key' => $data_prv['apikey'],'code' => $web_token,'reff_id' => $ShennTRX,'target' => $tgt_token]);
    if($try['status'] == true) {
        $req_result = true;
        $req_provid = $try['data']['id'];
        $req_status = 'waiting';
        $req_note = ($try['message'] == '') ? 'Your order will be processed immediately.' : $try['message'];
    } else {
        $req_result = false;
        $req_message = isset($try['message']) ? $try['message'] : 'Connection Failed!';
    }
} else if($provider == 'X') {
    $req_result = true;
    $req_provid = $ShennTRX;
    $req_note = 'Your order will be processed immediately.';    
    // $try = TrxNotifier($sess_username,$service,$tgt_token);
    // if($try['result'] == true) {
    //    $req_result = true;
    //    $req_provid = $ShennTRX;
    //    $req_status = 'waiting';
    //    $req_note = 'Pesanan akan diproses secepatnya.';
    // } else {
    //   $req_result = false;
    //   $req_message = 'Gagal melakukan pemesanan, silahkan coba lagi nanti.';
    // }
}