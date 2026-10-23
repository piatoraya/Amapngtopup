<?php
require '../../../connect.php';
require _DIR_('library/session/admin');

function iss($x) {
    return isset($x) ? $x : '';
}

if(isset($_POST['save'])) { 
   $on = filter($_POST['ac_depo']);
   $of = filter($_POST['of_depo']);
   
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
    } else {
        if($call->query("UPDATE conf SET c1 = '$on', c2 = '$of' WHERE code = 'depofeat'") == true) {
            ShennXit(['type' => true,'message' => 'Deposit time was successfully updated.']);
        } else {
            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
        }
    }
} if(isset($_POST['save_req'])) {   
      $pst_1 = iss($_POST['dy']);
      $pst_2 = iss($_POST['min']);
      
      if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
      } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
      } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
      } else {
          if($call->query("UPDATE conf SET c1 = '$pst_1', c2 = '$pst_2' WHERE code = 'deporeq'")) {
              ShennXit(['type' => true,'message' => 'Data berhasil diubah.']);
          }
      }
  }
  

require _DIR_('library/layout/header.admin');
?>
<div class="row">
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Deposit Set</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
                    <div class="form-group">
                        <label>Active</label>
                        <input type="time" class="form-control" name="ac_depo" value="<?= conf('depofeat',1) ?>" placeholder="">
                    </div>
                    <div class="form-group">
                        <label>Not Active</label>
                        <input type="time" class="form-control" name="of_depo" value="<?= conf('depofeat',2) ?>" placeholder="">
                    </div>
                    <div class="form-group text-right">                      
                        <button type="submit" name="save" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Deposit REQ</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
                    <div class="form-group">
                        <label>Days</label>
                        <select class="form-control" name="dy">
                            <option selected disabled><?= conf('deporeq',1) ?></option>
                            <?php
                            for($x = 0; $x <= 30; $x++) {
                                 print '<option value="'.$x.' Days">'.$x.' Days</option>';
                            } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Days</label>
                        <select class="form-control" name="min">
                            <option selected disabled><?= conf('deporeq',2) ?></option>
                            <?php
                            for($x = 0; $x <= 59; $x++) {
                                 print '<option value="'.$x.' Minute">'.$x.' Minute</option>';
                            } ?>
                        </select>
                    </div>
                    <div class="form-group text-right">                      
                        <button type="submit" name="save_req" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require _DIR_('library/layout/footer.user'); ?>