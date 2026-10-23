<?php
require '../../../connect.php';
require _DIR_('library/session/session');

$code = isset($_GET['s']) ? filter($_GET['s']) : '';
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if(!isset($_SESSION['user'])) exit("No direct script access allowed!");
    if($data_user['level'] !== 'Admin') exit("No direct script access allowed!");
    if(!isset($code) || !$code) exit("No direct script access allowed!");
    $search = $call->query("SELECT * FROM con_game WHERE code = '$code'");
    if($search->num_rows == 0) exit("No data found from the Code!");
    $data = $search->fetch_assoc();
    $place_json = json_decode($data['place'], true);   
    $LannConfirmed = 'editf';
    
    if($_POST) {
        $web_code = filter($_POST['web_code']);
        if($LannConfirmed == 'editf') {
            $post_form = filter($_POST['inform']);
            $post_place1 = filter($_POST['place_one']);
            $post_place2 = filter($_POST['place_two']);
           
            $json_place = json_encode(['place_one' => $post_place1,'place_two' => $post_place2]);              
            $post_cgame = filter($_POST['cid']);
            $post_valid = (!$post_cgame) ? '0' : '1';
           
            if($result_csrf == false) {
                print sessResult(false,'System Error, please try again later.');
            } else if($_CONFIG['lock']['status'] == true) {
                print sessResult(false,$_CONFIG['lock']['reason']);           
            } else if(!in_array($data_user['level'],['Admin'])) {
                print sessResult(false,'You do not have access to use this feature.');
            } else {
                $upd = $call->query("UPDATE con_game SET form = '$post_form', place = '$json_place', validate = '$post_valid', code_id = '$post_cgame' WHERE code = '$web_code'");
                if($upd == true) {
                    print sessResult(true,'Successfully updated.');
                } else {
                    print sessResult(false,'Trauble');
                }                
            }           
        } die;
    }
?>
<div id="sess-result-modal"></div>
<form method="POST" id="form-edit">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">    
    <input type="hidden" name="web_code" value="<?= $data['code'] ?>">
    <div class="form-group">
        <label>Input Data</label>
        <select class="form-control" name="inform">
           <?= select_opt($data['form'],'0','Not Active') ?>
           <?= select_opt($data['form'],'1','1 | Form') ?>
           <?= select_opt($data['form'],'2','2 | Form') ?>
        </select>           
    </div>
    <div class="row">
        <div class="form-group col-md-6 d-none" id="place1">
            <label>Placeholder {1}</label>
            <input type="text" class="form-control" name="place_one" value="<?= $place_json['place_one'] ?>">
        </div>
        <div class="form-group col-md-6 d-none" id="place2">
            <label>Placeholder {2}</label>
            <input type="text" class="form-control" name="place_two" value="<?= $place_json['place_two'] ?>">                
        </div>       
    </div>
    <hr>
    <div class="form-group">
        <label>Code Game (Checker)</label>
        <input type="text" class="form-control" name="cid" value="<?= $data['code_id'] ?>">
        <small>* Memasukan code game berarti Anda mengaktifkan Validasi Game</small>
    </div>    
    <hr>
    <div class="row">
        <div class="form-group col-6">
            <button type="button" name="reset" class="btn btn-danger btn-block" data-dismiss="modal"> BACK </button>
        </div>
        <div class="form-group col-6">
            <button type="button" onclick="LannAct('#form-edit','<?= base_url('admin/game-feature/modal/edit-form?s='.$code) ?>');" class="btn btn-primary btn-block"> EDIT </button>      
        </div>
    </div>
</form>
<script type="text/javascript">
   $(document).ready(function() {
      var s = $("select[name=inform]").val();
      if(s == 1) {
         $("#place1").removeClass('d-none');
      } else if(s == 2) {
         $("#place1").removeClass('d-none');
         $("#place2").removeClass('d-none');      
      } else {
         $("#place1").addClass('d-none');
         $("#place2").addClass('d-none');
      }
      $("select[name=inform]").change(function() {         
          var x = $("select[name=inform]").val();
          $("#place1").addClass('d-none');        
          $("#place2").addClass('d-none');
          
          if(x == 2) {
             $("#place1").removeClass('d-none');
             $("#place2").removeClass('d-none');
          } else if(x == 1) {
             $("#place1").removeClass('d-none');
          }
      });
   });
</script>
<?php
} else {
    exit("No direct script access allowed!");
}
?>