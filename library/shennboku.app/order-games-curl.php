<?php
if(!isset($call) && !isset($provider) && !isset($data_prv))
    die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");

if($provider == 'VP') {
    $try = $curl->connectPost($data_prv['link'].'game-feature',['key' => $data_prv['apikey'],'sign' => md5($data_prv['userid'].$data_prv['apikey']),'type' => 'order','service' => $post_srv,'data_no' => $post_tgt,'data_zone' => $post_zon]);
    if($try['result'] == true) {
        $req_result = true;
        $req_provid = $try['data']['trxid'];
        $req_note = $try['data']['note'];
    } else {
        $req_result = false;
        $req_message = isset($try['message']) ? $try['message'] : 'Connection Failed!';
    }
} else if($provider == 'X') {
    $req_result = true;
    $req_provid = $ShennTRX;
    $req_note = 'Pesanan akan diproses secepatnya.';
    // $tryd = (!$post_zon) ? $post_tgt : "$post_tgt-$post_zon";
    // $try = TrxNotifier($sess_username,$service,$tryd);
    // if($try['result'] == true) {
    //     $req_result = true;
    //     $req_provid = $ShennTRX;
    //     $req_note = 'Pesanan akan diproses secepatnya.';
    // } else {
    //     $req_result = false;
    //     $req_message = 'Gagal melakukan pemesanan, silahkan coba lagi nanti.';
    // }
}