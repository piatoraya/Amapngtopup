<?php
require __DIR__.'/../_guard.php';
set_time_limit(240);
require '../../../connect.php';

// VARIABLE
$periode = conf('benefit-deposit',3);
$periode = rdate('Y-m-d','-'.$periode.' days');
$min = conf('benefit-deposit',1);
$benefit = conf('benefit-deposit',2);
$jangka_periode = conf('benefit-deposit',4);
$jangka_periode = rdate('Y-m-d H:i:s','+'.$jangka_periode.' days');

$search = $call->query("SELECT * FROM users WHERE status = 'active'");
while($row = $search->fetch_assoc()) {
    $user = $row['username'];
    $search_benefit = $call->query("SELECT * FROM benefit WHERE type = 'deposit' AND user = '$user'");
    
    $total = $call->query("SELECT SUM(amount + uniq) AS total FROM deposit WHERE status = 'paid' AND user = '$user'")->fetch_assoc()['total'];

    if($min < $total) {
        if($search_benefit->num_rows == 0) {
            if($row['level'] !== 'Admin') {
                if($call->query("UPDATE users SET level = '$benefit' WHERE username = '$user'") == true) {
                    $call->query("INSERT INTO benefit VALUES (null,'$user','$benefit','deposit','$dtme','$dtme')");
                
                    print '<font color="green"><pre>';
                    print "{$user} --> ({$benefit})";
                    print '</pre></font>';
                }
            }
        }
    } else {
        if($search_benefit->num_rows == 1) {
            $row_benefit = $search_benefit->fetch_assoc();
            if($dtme >= $row_benefit['date_ex']) {
                if($row['level'] !== 'Admin') {
                    if($call->query("UPDATE users SET level = 'Basic' WHERE username = '".$row_benefit['user']."'") == true) {
                        $call->query("DELETE FROM benefit WHERE user = '".$row_benefit['user']."' AND type = 'deposit'");              
                        print '<font color="green"><pre>';
                        print "{$user} --> Berhasil menurunkan tingkat {Basic}";
                        print '</pre></font>';
                    }
                }
            } else {
                print '<font color="red"><pre>';
                print "{$user} --> {$row_benefit['date_ex']} --> menunggu expired...";
                print '</pre></font>';
            }
        }
        
        print '<font color="red"><pre>';
        print "{$user} --> Masih belum terselesaikan...";
        print '</pre></font>';
        
    }        
}