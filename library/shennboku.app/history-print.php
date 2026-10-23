<?php
if(!isset($call)) die("Lann Digital Indonesia");

if(isset($_POST['print_ppob'])) {
    $web_token = filter(base64_decode($_POST['web_token']));
    $post_price = isset($_POST['price']) ? filter($_POST['price']) : $call->query("SELECT price FROM trx_ppob WHERE wid = '$web_token'")->fetch_assoc()['price'];
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if($call->query("SELECT * FROM trx_ppob WHERE wid = '$web_token'")->num_rows == 0) {
        ShennXit(['type' => false,'message' => 'Data not found.']);
    } else {               
        redirect(0,base_url('order/print_ppob?wid='.$web_token.'&price='.$post_price.''));       
    }
} if(isset($_POST['print_game'])) {
    $web_token = filter(base64_decode($_POST['web_token']));
    $post_price = isset($_POST['price']) ? filter($_POST['price']) : $call->query("SELECT price FROM trx_game WHERE wid = '$web_token'")->fetch_assoc()['price'];
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if($call->query("SELECT * FROM trx_game WHERE wid = '$web_token'")->num_rows == 0) {
        ShennXit(['type' => false,'message' => 'Data not found.']);
    } else {               
        redirect(0,base_url('order/print_game?wid='.$web_token.'&price='.$post_price.''));       
    }
}