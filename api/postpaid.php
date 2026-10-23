<?php
header('Content-Type: application/json');
require '../connect.php';

if(conf('xtra-fitur',1) != 'true') ShennDie(['result' => false,'data' => null,'message' => 'Fitur tidak tersedia.']);
if($_CONFIG['mt']['api'] == 'true') ShennDie(['result' => false,'data' => null,'message' => 'Ada pemeliharaan sistem API, coba lagi nanti.']);
if(isset($_POST['key']) && isset($_POST['sign']) && isset($_POST['type'])) {
    $key = filter($_POST['key']);
    $sign = filter($_POST['sign']);
    $type = strtr(filter($_POST['type']), [
        'inq-pasca' => 'inq',
        'inquiry' => 'inq',
        'pay-pasca' => 'pay',
        'payment' => 'pay',
        'layanan' => 'service',
        'services' => 'service'
    ]);

    $rl_ident = 'ip:'.$client_ip;
    if(shenn_rl_api_check($rl_ident, 30, 5)['result'] == false) {
        ShennDie(['result' => false,'data' => null,'message' => 'Too many failed API requests. Please try again later.']);
    }

    $s_api = $call->query("SELECT * FROM users_api WHERE ukey = '$key'");
    if($s_api->num_rows == 0) {
        shenn_rl_api_fail($rl_ident);
        ShennDie(['result' => false,'data' => null,'message' => 'API Key not registered.']);
    }
    $r_api = $s_api->fetch_assoc();
    $username = $r_api['user'];
    $data_user = data_user($username);
    if($data_user == false) {
        shenn_rl_api_fail($rl_ident);
        ShennDie(['result' => false,'data' => null,'message' => 'User not found.']);
    }
    if($sign <> md5($r_api['uid'].$r_api['ukey'])) {
        shenn_rl_api_fail($rl_ident);
        ShennDie(['result' => false,'data' => null,'message' => 'API Signature not valid.']);
    }
    if(check_ip($client_ip, $r_api['whitelist']) == false) {
        shenn_rl_api_fail($rl_ident);
        ShennDie(['result' => false,'data' => null,'message' => 'IP '.$client_ip.' is not permitted']);
    }
    if(!in_array($type, ['inq', 'pay', 'status', 'service'])) ShennDie(['result' => false,'data' => null,'message' => 'Type \''.$type.'\' is not valid']);
    $sess_username = $data_user['username'];
    $_CONFIG['lock'] = check_lock($sess_username);
    
    if($type == 'inq')      require 'postpaid-inq.php';
    if($type == 'pay')      require 'postpaid-pay.php';
    if($type == 'status')   require 'postpaid-status.php';
    if($type == 'service')  require 'postpaid-service.php';
} else {
    ShennDie(['result' => false,'data' => $_POST,'message' => 'Request not detected.']);
}