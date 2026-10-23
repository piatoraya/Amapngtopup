<?php
require '../../connect.php';
require _DIR_('library/session/session');

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if(!isset($_POST['hash'])) exit("No direct script access allowed!");
    $hash = filter(base64_decode($_POST['hash']));
    
    $search = $call->query("SELECT * FROM trx_ppob WHERE wid = '$hash'");
    if($search->num_rows == 1) {
        $row = $search->fetch_assoc();
        print json_encode(['result' => true,'status' => $row['status']]);       
    }
} else {
    exit("No direct script access allowed!");
}
    