<?php
require __DIR__.'/../_guard.php';
set_time_limit(240);
require '../../../connect.php';

$benefit_target = conf('benefit-transaksi',1);
$search = $call->query("SELECT * FROM users WHERE status = 'active' AND level != 'Admin' ORDER BY id DESC");
if($search->num_rows > 0) {
    while($user_data = $search->fetch_assoc()) {
        $count_total = $call->query("SELECT SUM(price) AS total FROM trx_ppob WHERE status = 'success' AND MONTH(date_cr) = '".date('m')."' AND user = '".$user_data['username']."'")->fetch_assoc()['total']
            + $call->query("SELECT SUM(price) AS total FROM trx_game WHERE status = 'success' AND MONTH(date_cr) = '".date('m')."' AND user = '".$user_data['username']."'")->fetch_assoc()['total'];              
        $see_benefit = $call->query("SELECT * FROM benefit WHERE user = '".$user_data['username']."'");        
        if($see_benefit->num_rows == 0) {            
            if($benefit_target < $count_total) {
                $ins = $call->query("INSERT INTO benefit VALUES (null,'".$user_data['username']."','Premium','transaksi','$dtme','$dtme')");
                if($ins == true) {
                    $call->query("UPDATE users SET level = 'Premium' WHERE username = '".$user_data['username']."'");                      
                    print '<font color="green"><pre>'.$user_data['username'].' {Premium}</pre></font>';
                }
            } 
        } else {
            $user_benefit = $see_benefit->fetch_assoc();
            if($count_total < $benefit_target) {
                $del = $call->query("DELETE FROM benefit WHERE user = '".$user_benefit['user']."'");
                if($del == true) {
                    $call->query("UPDATE users SET level = 'Basic' WHERE username = '".$user_benefit['user']."'");
                    print '<font color="green"><pre>'.$user_data['username'].' {Basic}</pre></font>';                   
                }
            }
        }
    }
}