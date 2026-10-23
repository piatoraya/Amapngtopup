<?php
require '../../connect.php';
require _DIR_('library/session/admin');

if(isset($_POST['send_msg'])) {
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
    } else {  
       
        $try = ['result' => true];        
        if($try['result'] == true) {
             redirect(0,base_url('admin/addon/result?msg='.conf('addon1',4)));
             #ShennXit(['type' => true,'message' => 'Pesan barhasil dikirim.'],$go);
        } else {
             ShennXit(['type' => false,'message' => 'TRAUBLE!']);
        }
    }
} if(isset($_POST['upd_msg'])) {
      $post_msg = $_POST['msg'];
      
      if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
      } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
      } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
      } else {  
          
          $upd = $call->query("UPDATE conf SET c4 = '".base64_encode($post_msg)."' WHERE code = 'addon1'");
          if($upd == true) {
              ShennXit(['type' => true,'message' => 'Message berhasil diubah.']);
          }
      }
}

require _DIR_('library/layout/header.admin');
?>
<div class="row">
    <div class="col-12 col-md-5">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Shortcut</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>{name}</td><td>USER Name, Sample: Lann Digital</td></tr>               
                        <tr><td>{balance}</td><td>USER Balance, sample: 30.000</td></tr>                                          
                        </tbody>
                </table>
                <small class="text-danger">Huruf kapital dapat mempengaruh.</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-7">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Broadcast</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
                    <div class="form-group">
                        <label>Message</label>
                        <textarea class="form-control" rows="8" id="msg" name="msg"><?= base64_decode(conf('addon1',4)) ?></textarea>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" name="upd_msg" class="btn btn-success waves-effect waves-light">(1). Update MSG</button>
                        <button type="submit" name="send_msg" class="btn btn-primary waves-effect waves-light">(2). Sender MSG</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require _DIR_('library/layout/footer.user'); ?>