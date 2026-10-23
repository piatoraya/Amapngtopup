<?php
require '../../connect.php';
require _DIR_('library/session/admin');

$id = filter(base64_decode(nl2br($_GET['id'])));

$q = squery("SELECT * FROM users WHERE status = 'active'");
while($f = $q->fetch_object()) {
       
       $send = $lann_sender_message($f->phone,$id);
       if($send['status'] == 'ok') {      
          # ShennXit(['type' => true,'message' => 'Broadcast successfully']);
       }
}
require _DIR_('library/layout/header.user');
?>

<pre>Pesan berhasil dikirim! (Harap tunggu... [kami akan alihkan (<div id="msg">])</pre>

<script>
    var url = "https://ampangpedia.com/admin/config/member-toast"; // membuat url tujuan
    var count = 10; // membuat hitungan kedalam detik
    function countDown() {
        if(count > 0) {
            count--;
            var waktu = count + 1;
                $('#msg').html(waktu);
                    setTimeout("countDown()", 1000);
        } else {
            window.location.href = url;
        }
    }
countDown();
</script>