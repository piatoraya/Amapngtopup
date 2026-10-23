<?php
require __DIR__.'/_guard.php';
set_time_limit(240);
require '../../connect.php';
require 'helper.php';

$getkey = isset($_GET['noresult']) ? false : true;
$search = $call->query("SELECT * FROM trx_ppob WHERE status = 'error' AND refund = '0'");
if($search->num_rows == 0) {
    print '<font color="red"><pre>Order Error not found</pre></font>';
} else {
    while($row = $search->fetch_assoc()) {
        $wid = $row['wid'];
        $user = $row['user'];
        $price = $row['price'];
        $uplink = $call->query("SELECT * FROM users WHERE username = '$user'")->fetch_assoc()['uplink'];
        
        $up = $call->query("UPDATE users SET balance = balance+$price WHERE username = '$user'");
        $up = $call->query("INSERT INTO mutation VALUES (null,'$user','+','$price','Pulsa PPOB Refunds :: $wid','$date $time')");
        $up = $call->query("UPDATE trx_ppob SET price = '0', profit = '0', refund = '1' WHERE wid = '$wid'");
        if($up == true) {
            if($getkey == true) print '<font color="green"><pre>[+] '.$wid.' {Successfully returned '.currency($price).' funds to '.$user.'}</pre></font><hr>';
            if($row['spoint'] == '1' && $call->query("SELECT * FROM users WHERE referral = '$uplink'")->num_rows == 1) {
                $call->query("UPDATE users SET point = point-".$row['point']." WHERE referral = '$uplink'");
                $call->query("UPDATE trx_ppob SET point = '0' WHERE wid = '$wid'");
            }
        } else {
            if($getkey == true) print '<font color="red"><pre>[!] '.$wid.' {Database Error}</pre></font><hr>';
        }
    }
}
squery("UPDATE conf SET c2 = '$date $time' WHERE code = 'status-refund-access'");