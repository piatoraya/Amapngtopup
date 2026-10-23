<?php
require __DIR__.'/library/cron/_guard.php';
require 'connect.php';

$dt_up = conf('restart-wapi',1);
$dt_start = conf('restart-wapi',2);

if($dtme >= $dt_start) {
    $wp_start = $WAPI->deviceRestart();
    if($wp_start['result'] == true) {
        $returnRestart = date('Y-m-d H:i:s', strtotime("+1 hours", strtotime($dtme)));
        $call->query("UPDATE conf SET c1 = '$dtme', c2 = '$returnRestart' WHERE code = 'restart-wapi'");
        $result[$dt_up] = ['status' => 'OK!','message' => 'BERHASIL RESTART'];
    } else {
        $result[$dtme] = ['status' => false,'message' => 'GAGAL RESTART'];
    }
} else {
    $result[$dt_start] = ['status' => false,'message' => 'Processing date...'];
} print '<pre>'.json_encode($result).'</pre>';