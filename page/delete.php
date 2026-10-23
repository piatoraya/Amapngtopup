<?php
require '../connect.php';
require _DIR_('library/session/session');

if(isset($_POST['delet'])) {
    $web_user = $sess_username;
    $web_duedtme = date('Y-m-d H:i:s', strtotime('+7 days', strtotime($dtme)));
    
    if($result_csrf == false) {
        ShennXit(['result' => true,'message' => 'System error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if($call->query("SELECT * FROM delete_akun WHERE user = '$web_user'")->num_rows == 1) {
        ShennXit(['type' => false,'message' => 'Your account was previously on the list.']);    
    } else {
        if($call->query("INSERT INTO delete_akun VALUES (null,'$web_user','$dtme','$web_duedtme')") == true) {           
            $call->query("DELETE FROM users_cookie WHERE cookie = '".filter($_COOKIE['token'])."'");
            setcookie('ssid', '', time() - 3600, '/', $_SERVER['HTTP_HOST']);
            setcookie('token', '', time() - 3600, '/', $_SERVER['HTTP_HOST']);          
            unset($_SESSION['user']);
            ShennXit(['type' => true,'message' => 'Your account has been successfully registered for deletion, please wait 7 days and do not log in.'],base_url('auth/login'));
        } else {
            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
        }
        
    }
}

require _DIR_('library/layout/header.user');
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Delete Account</h4>
            </div>
            <div class="card-body">
                <?= base64_decode(conf('delete_akun',1)) ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" id="csrf_token" value="<?= $csrf_string ?>">
                    <button type="submit" name="delet" class="btn btn-primary btn-block">Penghapusan</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require _DIR_('library/layout/footer.user'); ?>