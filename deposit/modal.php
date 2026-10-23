<?php
require '../connect.php';
require _DIR_('library/session/session');

$get_id = isset($_GET['id']) ? filter($_GET['id']) : '';
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($data_user)) {
    if(!isset($_SESSION['user'])) exit("No direct script access allowed!");
    if(!$get_id) exit("No direct script access allowed!");
    $search = $call->query("SELECT * FROM deposit WHERE id = '$get_id' AND user = '$sess_username' AND status IN('unpaid','process') AND payment NOT IN ('transfer','voucher')");
    if($search->num_rows == 0) exit("Request not Detected, please try again later.");
    $row = $search->fetch_assoc();
    
    $payname = $call->query("SELECT * FROM deposit_payment WHERE code = '".$row['payment']."' ORDER BY name ASC LIMIT 1");
    $payname = ($payname->num_rows == 1) ? $payname->fetch_assoc()['name'] : 'Unknown';
    $paytotal = $row['amount']+$row['fee']+$row['uniq'];
    
    $search_method = $call->query("SELECT * FROM deposit_method WHERE method = '".$row['payment']."'");
    if($search_method->num_rows == 1) $data_method = $search_method->fetch_assoc();
    
    $validasi_qris = (stristr(strtolower($data_method['method']),'qris')) ? true : false;
    $type_qris = (stristr(strtolower($data_method['method']),'fast')) ? true : false;
    
    function center_out($x) { return '<center>'.$x.'</center>'; }
    function image_out($x) { return '<img src="'.$x.'" height="200" class="img-thumbnail">'; }                  
     
    if($data_method['auto'] == 1) {
        if($data_method['type'] == 'bank') {
            $output = '<div style="font-size:0.8rem;display:inline;">Tujuan<span class="justify-content-end" style="float:right;font-size:0.8rem;">'.explode(' A/n ',$row['note'])[0].'</span></div><br>
        <div style="font-size:0.8rem;display:inline;">Atas Nama<span class="justify-content-end" style="float:right;font-size:0.8rem;">'.explode(' A/n ',$row['note'])[1].'</span></div><br>';
        } else if($data_method['type'] == 'emoney' && $validasi_qris == false) {
            $output ='<hr>
            <div class="form-group">
                <a href="'.$row['note'].'" class="btn btn-primary btn-block text-white"> Bayar </a>
            </div>';
        } else if($validasi_qris == true) {
            $output = '<hr>'.center_out(image_out($row['note']));
        } else if($data_method['type'] == 'virtual') {
            $output = '<div style="font-size:0.8rem;display:inline;">Tujuan<span class="justify-content-end" style="float:right;font-size:0.8rem;">'.explode(' A/n ',$row['note'])[0].'</span></div>';
        } else {
            $output = "";
        }
    } else {
       $output = '<div style="font-size:0.8rem;display:inline;">Tujuan<span class="justify-content-end" style="float:right;font-size:0.8rem;">'.explode(' A/n ',$row['note'])[0].'</span></div><br>
        <div style="font-size:0.8rem;display:inline;">Atas Nama<span class="justify-content-end" style="float:right;font-size:0.8rem;">'.explode(' A/n ',$row['note'])[1].'</span></div><br>';
    }
    
?>
<form method="POST">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
    <h4>Deposit Information</h4>
    <div style="font-size:0.8rem;display:inline;">Method<span class="justify-content-end" style="float:right;font-size:0.8rem;"><?= $payname ?></span></div><br>
    <div style="font-size:0.8rem;display:inline;">Transfer<span class="justify-content-end" style="float:right;font-size:0.8rem;"><?= 'Rp '.currency($paytotal) ?></span></div><br>
   
    <?= $output ?>
    
    <hr>
    <?php if($row['status'] == 'process' && $type_qris == false) { ?>
    <div class="form-group">
        <a href="javascript:;" <?= onclick_modal('Cair Instant',base_url('deposit/instant-modal?q='.base64_encode($row['id'])),'','lg') ?> class="btn btn-primary text-white btn-block"> Cair Instant </a>
    </div>
    <?php } ?>
    <div class="form-group">
        <button type="button" class="btn btn-danger btn-block" data-dismiss="modal"> Close </button>
    </div>
</form>
<?php
    require _DIR_('library/layout/modal');
} else {
    exit("No direct script access allowed!");
}
?>