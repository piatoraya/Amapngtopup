<?php
require __DIR__.'/_guard.php';
require '../../connect.php';

function delete_action($table,$user) {
    global $call;
    return $call->query("DELETE FROM $table WHERE user = '$user'");
}

$search = $call->query("SELECT * FROM delete_akun");
if($search->num_rows > 0) {
    while($row = $search->fetch_assoc()) {
        $web_user = $row['user'];
        if($dtme >= $row['expired']) {
            if($call->query("UPDATE users SET status = 'suspend' WHERE username = '$web_user'") == true) {
                delete_action('users_cookie',$web_user);
                delete_action('users_location',$web_user);
                delete_action('logs',$web_user);
                delete_action('mutation',$web_user);
                delete_action('trx_ppob',$web_user);
                delete_action('trx_socmed',$web_user);
                delete_action('trx_game',$web_user);
                delete_action('deposit',$web_user);     
                delete_action('voucher',$web_user);      
            }                  
        } else {
            print '<font color="red"><pre>[!] Not yet time...</pre></font>';
        }
    }
}