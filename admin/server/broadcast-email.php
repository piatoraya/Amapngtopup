<?php
require '../../connect.php';
require _DIR_('library/session/admin');

if(isset($_POST['sender'])) { 
   $mg = filter($_POST['mg']);
   
   if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
   } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
   } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
   } else if(!$mg) {
        ShennXit(['type' => false,'message' => 'There is still a blank form.']);
   } else {           
        
        $data = $call->query("SELECT * FROM users WHERE status = 'active'");
        while($f = mysqli_fetch_array($data)) {
        $em = $f['email'];
        $name = $f['name'];
        $_SESSION['temp']['pesan'] = base64_encode(str_replace("\r\n",'', $mg));
        
        $send_mail = mailer($_MAILER, [
            'dest' => $em,
            'name' => $name,
            'subject' => 'This is our daily notification',
            'message' => $_SESSION['temp']['pesan'],
            'is_template' => 'yes'
        ]);
        
        
        if($send_mail == true) { 
           ShennXit(['type' => true,'message' => 'Sender successfully to email.']);
           } else {
             }
        }
    }
}
require _DIR_('library/layout/header.admin');
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Broadcast Email</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
                    <div class="form-group">
                        <label>Message</label>
                        <textarea class="form-control" name="mg" rows="5"></textarea>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" name="reset" class="btn btn-danger waves-effect waves-light">Reset</button>
                        <button type="submit" name="sender" class="btn btn-primary waves-effect waves-light">Sender</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require _DIR_('library/layout/footer.user'); ?>