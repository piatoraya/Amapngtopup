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
    $LannConfirmed = 'adds';
    
    if($_POST) {
        $web_code = filter($_POST['web_code']);
        if($LannConfirmed == 'adds') {
            $pst1 = filter($_POST['v1']);
            $pst2 = filter($_POST['n1']);
           
            if($result_csrf == false) {
                print sessResult(false,'System Error, please try again later.');
            } else if($_CONFIG['lock']['status'] == true) {
                print sessResult(false,$_CONFIG['lock']['reason']);
            } else if(!in_array($data_user['level'],['Admin'])) {
                print sessResult(false,'You do not have access to use this feature.');
            } else if($call->query("SELECT * FROM con_game_server WHERE value = '$pst1' AND name = '$pst2'")->num_rows == 1) {
                print sessResult(false,'Value and Name already used.');
            } else if(!$pst1 || !$pst2) {
                print sessResult(false,'There is still a blank form.');
            } else if(!$web_code) {
                print sessResult(false,'Web Code not detected.');            
            } else {
                if($call->query("INSERT INTO con_game_server VALUES (null,'$web_code','$pst1','$pst2')") == true) {
                    $call->query("UPDATE con_game SET server = '1' WHERE code = '$web_code'");
                    print sessResult(true,'Server successfully added.');                    
                } else {
                    print sessResult(false,'Trauble');                    
                }
            }
            die;
        }
    }
       
?>
<div id="sess-result-modal"></div>
<form method="POST" id="form-edit">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">    
    <input type="hidden" name="web_code" value="<?= $data['code'] ?>">
    <div class="form-group">
        <label>Code</label>
        <input text="text" disabled class="form-control" value="<?= $data['code'] ?>">
    </div>       
    <div class="row">
        <div class="form-group col-6">
            <label>Value</label>
            <input text="text" name="v1" class="form-control">
        </div>
        <div class="form-group col-6">
            <label>Name</label>
            <input text="text" name="n1" class="form-control">
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="form-group col-6">
            <button type="button" name="reset" class="btn btn-danger btn-block" data-dismiss="modal"> BACK </button>
        </div>
        <div class="form-group col-6">
            <button type="button" onclick="LannAct('#form-edit','<?= base_url('admin/game-feature/modal/add-server?s='.$code) ?>');" class="btn btn-primary btn-block"> ADD </button>
        </div>
    </div>
</form>
<?php
} else {
    exit("No direct script access allowed!");
}
?>