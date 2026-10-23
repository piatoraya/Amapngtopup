<?php
require '../connect.php';
require _DIR_('library/session/user');

$mt_bank = false; // MAINTENANCE BANK (true=mt | false:tidak)

if(isset($_SESSION['LANN_SESSION'])) redirect(0,base_url('order/bank-detail'));

if(isset($_POST['verify'])) { 
    $bank_category = filter(base64_decode($_POST['bank_category']));
    $bank_rekening = filter($_POST['bank_rekening']);
    $bank_amount = filter($_POST['bank_amount']);
    
    $post_query = squery("SELECT * FROM srv_bank WHERE code = '$bank_category'");
    $post_wid = date('YmdHis').random_number(2);
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($mt_bank == true) {
        ShennXit(['type' => false,'message' => 'This service is currently unavailable, please try again later.']);   
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!$bank_category || !is_numeric($bank_rekening) || !is_numeric($bank_amount)) {
        ShennXit(['type' => false,'message' => 'There is still an empty form.']);
    } else if($post_query->num_rows == 0) {
        ShennXit(['type' => false,'message' => 'Banking is currently unavailable.']);
    } else if($data_user['balance'] < $bank_amount) {
        ShennXit(['type' => false,'message' => 'Your balance is insufficient.']);
    } else if($data_user['balance'] < ($bank_amount+$_CONFIG['hold'][$data_user['level']])) {
        ShennXit(['type' => false,'message' => 'Your minimum account balance is '.number_format($_CONFIG['hold'][$data_user['level']]).'.']);  
    } else {    
        $prov = $call->query("SELECT * FROM provider WHERE code = 'ATL'")->fetch_assoc();
        $connect = $curl->connectPost($prov['link'].'transfer/cek_rekening',['api_key' => $prov['apikey'],'bank_code' => $bank_category,'account_number' => $bank_rekening]);
        $row = $post_query->fetch_assoc();      
        if(isset($connect['status'])) {
            if($connect['status'] == false) {
                ShennXit(['type' => false,'message' => $connect['message']]);
            } else {
                $_SESSION['LANN_SESSION'] = [
                    'bank_code' => $bank_category,
                    'bank_name' => $row['name'],
                    'bank_number' => $bank_rekening,
                    'bank_pemilik' => $connect['data']['nama_pemilik'],
                    'bank_amount' => $bank_amount
                ];                      
                
                redirect(0,base_url('order/bank-detail'));
                
            }
        } else {
            ShennXit(['type' => false,'message' => 'Connected failed!']);
        }
    }
}

require _DIR_('library/layout/header.user');
?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Transfer Bank</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
                    
                    <div class="form-group">
                        <label>Pilih Bank Tujuan</label>
                        <select class="form-control" name="bank_category" id="bank_category">
                            <?php
                            $s = $call->query("SELECT DISTINCT code, name FROM srv_bank ORDER BY name ASC");
                            print '<option selected disabled>- Choose One -</option>';
                            while($f = $s->fetch_assoc()) {
                                print '<option value="'.base64_encode($f['code']).'">'.$f['name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>No. Rekening</label>
                        <input type="number" pettern="[0-9]" class="form-control" name="bank_rekening">
                    </div>
                    
                    <div class="form-group">
                        <label>Jumlah Transfer</label>
                        <input type="number" pettern="[0-9]" class="form-control" name="bank_amount">
                    </div>
                    <button type="submit" name="verify" class="btn btn-primary waves-effect waves-light col-12">Checking</button>                    
                </form>
            </div>
        </div>
    </div>
</div>
<?php require _DIR_('library/layout/footer.user'); ?>
<script>
$(document).ready(function() {
    $('#bank_category').select2();
});
</script>