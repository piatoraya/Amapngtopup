<?php
require '../../connect.php';
require _DIR_('library/session/session');

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($data_user)) {
    if(!isset($_SESSION['user'])) exit('No direct script access allowed!');
    if(!isset($_POST['meter'])) exit('No direct script access allowed!');
    if(empty($_POST['meter'])) exit("Data tidak ditemukan");
    
    $no = filter($_POST['meter']);
    $ukey = 'Cpy7hPlRh5fVy3AnDqrCznAz4ssIZK0iHkQbVQjOTg3ji700s2c1dXAWMaNrpMMO';
    $uid = 't2gy4xyr';
    $secure = '123456';
    
    $try = $curl->connectPost('https://client.lanndev.site/api/pln-checker',['key' => $ukey, 'sign' => md5($uid.$ukey),'secure' => $secure,'no_meter' => $no]);
   
    if($try['result'] == true) {
        print $try['data']['customerName'];
    } else {
        print isset($try['message']) ? 'Data tidak ditemukan' : '';
    }
   
} else {
	exit('No direct script access allowed!');
}