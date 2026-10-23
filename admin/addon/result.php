<?php
require '../../connect.php';
require _DIR_('library/session/admin');

function broadcast($u,$n) {
    $msg = strtr(base64_decode(conf('addon1',4)), ['{name}' => data_user($u,'name'),'{balance}' => number_format(data_user($u,'balance'))]);
    //if(conf('wapisender-cfg', 4) == 'false') return ['result' => true];
    return lann_sender_message($n,$msg);
}

$get = filter($_GET['msg']);

$q = $call->query("SELECT * FROM users WHERE status = 'active'");
while($f = $q->fetch_object()) {
    
      $try = broadcast($f->username,$f->phone);
}

require _DIR_('library/layout/header.admin');
?>

<?php require _DIR_('library/layout/footer.user'); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>
   Swal.fire(
      'Good job!',
      'Pesan berhasil dikirim.',
      'success'
   ).then(function() {
       window.location = "https://ampangpedia.com/admin/config/member-toast";
});
</script>