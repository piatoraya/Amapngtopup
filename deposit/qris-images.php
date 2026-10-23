<?php
require '../connect.php';
require _DIR_('library/session/session');

$TrxID = isset($_GET['id']) ? $_GET['id'] : (is_array($_GET) ? key($_GET) : '');
$TrxID = (is_string($TrxID) && preg_match('/^[A-Za-z0-9\-]{1,64}$/', $TrxID)) ? filter($TrxID) : '';
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($data_user)) {
    if(!isset($_SESSION['user'])) exit("No direct script access allowed!");
    if(!$TrxID) exit("No direct script access allowed!");
    $search = $call->query("SELECT * FROM deposit WHERE wid = '$TrxID' AND user = '$sess_username' AND payment NOT IN ('transfer','voucher')");
    if($search->num_rows == 0) exit("No data found from the Request ID!");
    $data_key = $search->fetch_assoc();
    
    $payname = $call->query("SELECT * FROM deposit_payment WHERE code = '".$data_key['payment']."' ORDER BY name ASC LIMIT 1");
    $payname = ($payname->num_rows == 1) ? $payname->fetch_assoc()['name'] : 'Unknown';
    $paytotal = $data_key['amount']+$data_key['fee']+$data_key['uniq'];
    
    function centerD($x) { 
       return '<center>'.$x.'</center>'; 
    }
    
    function imgD($x) { 
       return '<img src="'.$x.'" height="200" class="img-thumbnail">';
    }
    
?>
<form method="POST">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
    <input type="hidden" id="web_token" name="web_token" value="<?= base64_encode($data_key['wid']) ?>">
    <h4>Deposit Information</h4>
    <div style="font-size:0.8rem;display:inline;">Method<span class="justify-content-end" style="float:right;font-size:0.8rem;"><?= $payname ?></span></div><br>
    <div style="font-size:0.8rem;display:inline;">Transfer<span class="justify-content-end" style="float:right;font-size:0.8rem;"><?= 'Rp '.currency($paytotal) ?></span></div>
    <hr>
    <?= centerD(imgD(base_url('qris-am.jpg'))) ?>
    <hr>
    <div class="row">
        <div class="form-group col-6">
            <button type="button" class="btn btn-danger btn-block" data-dismiss="modal"> Close </button>
        </div>
        <div class="form-group col-6">
            <a href="https://api.whatsapp.com/send/?phone=6285242283425" class="btn btn-success btn-block text-white"> Confirm </a>
        </div>
    </div>
</form>
<?php
} else {
    exit("No direct script access allowed!");
}
?>