<?php
require __DIR__.'/_guard.php';
require '../../connect.php';

$q = squery("SELECT * FROM deposit WHERE status = 'unpaid'");
while($f = $q->fetch_object()) {
       $end = $f->date_up;
       $uid = $f->id;
       
       if($end <= $dtme) {
           squery("UPDATE deposit SET status = 'cancelled' WHERE id = '$uid'");      
           print '<pre>'.$f->wid.' -> Berhasil dibatalkan<hr>';
       } else {
           print '<pre>'.$f->wid.' -> Masih dalam proses<hr>';
       }
}