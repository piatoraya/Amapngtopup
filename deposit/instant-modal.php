<?php
require '../connect.php';
require _DIR_('library/session/session');

$get_id = isset($_GET['q']) ? filter(base64_decode($_GET['q'])) : '';
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($data_user)) {
    if(!isset($_SESSION['user'])) exit("No direct script access allowed!");
    if(!isset($get_id) || !$get_id) exit("No direct script access allowed!");
    $search = $call->query("SELECT * FROM deposit WHERE id = '$get_id' AND status = 'process' AND payment NOT IN('transfer','voucher')");
    if($search->num_rows == 0) exit("No direct script access allowed!");
    $data = $search->fetch_assoc();
    $total_pembayaran = $data['amount']+$data['fee']+$data['uniq'];
    
    $search_method = $call->query("SELECT * FROM deposit_method WHERE method = '".$data['payment']."'");
    if($search_method->num_rows == 1) $data_method = $search_method->fetch_assoc();    
    $type_qris (stristr(strtolower($data_method['method']),'fast')) ? true : false;   
    if($type_qris == true) exit("No direct script access allowed!");
    
    $provider = $call->query("SELECT * FROM provider WHERE code = 'ATL'")->fetch_assoc();
    $connect = $curl->connectPost($provider['link'].'deposit/instant',['api_key' => $provider['apikey'],'id' => base64_decode($data['sender']),'action' => 'false']);
    //print_r($connect);
    if(isset($connect['status'])) {
        if($connect['status'] == true) {
            $nominal = $connect['data']['nominal'];
            $penanganan = $connect['data']['penanganan'];            
        }
    } else {
        exit("Connection Failed!");
    }
?>
<form method="POST">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
    <input type="hidden" id="web_token" name="web_token" value="<?= base64_encode($get_id) ?>">
    <div class="form-group">
        <label>Method</label>
        <input type="text" class="form-control" value="<?= $data['method'] ?>" disabled>
    </div>
    <h3>Sebelum Instant</h3>
    <div class="row">
        <div class="form-group col-6">
            <label>Uniq</label>
            <input type="text" class="form-control" value="<?= currency($data['uniq']) ?>" disabled>
        </div>
        <div class="form-group col-6">
            <label>Fee</label>
            <input type="text" class="form-control" value="<?= currency($data['fee']) ?>" disabled>
        </div>
    </div>
    <div class="form-group">
        <label>Total Diterima</label>
        <input type="text" class="form-control" value="<?= currency($data['amount']) ?>" disabled>
    </div>
    <hr>
    <h3>Sesudah Instant</h3>
    <div class="row">
        <div class="form-group col-6">
            <label>Uniq</label>
            <input type="text" class="form-control" value="<?= currency($data['uniq']) ?>" disabled>
        </div>
        <div class="form-group col-6">
            <label>Fee</label>
            <input type="text" class="form-control" value="<?= currency($data['fee']+$penanganan) ?>" disabled>
        </div>
    </div>
    <div class="form-group">
        <label>Total Diterima</label>
        <input type="text" class="form-control" value="<?= currency($nominal-$data['fee']-$penanganan) ?>" disabled>
    </div>
    <hr>
    <div class="row">
        <div class="form-group col-6">
            <button type="button" class="btn btn-danger btn-block" data-dismiss="modal"> BACK </button>
        </div>
        <div class="form-group col-6">
            <button type="submit" name="instant" class="btn btn-primary btn-block"> INSTANT </button>
        </div>
    </div>
</form>
<?php
} else {
    exit("No direct script access allowed!");
}
?>