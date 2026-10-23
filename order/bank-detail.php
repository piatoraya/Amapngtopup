<?php
require '../connect.php';
require _DIR_('library/session/user');

//unset($_SESSION['LANN_SESSION']);

if(!isset($_SESSION['LANN_SESSION'])) redirect(0,base_url('order/bank-transfer'));

if(isset($_SESSION['LANN_SESSION']) && isset($_POST['cancel'])) {
    unset($_SESSION['LANN_SESSION']);
    ShennXit(['type' => true,'message' => 'Transfer cancelled successfully.'],base_url('order/bank-transfer'));
}

$bank_fee = 2000; // FEE DARI ATLANTIC
$bank_profit = 1000; // PROFIT WEB KAMU
       
if(isset($_SESSION['LANN_SESSION']) && isset($_POST['transfer'])) {
    $data_session = $_SESSION['LANN_SESSION'];
    $bank_code = $data_session['bank_code'];
    $bank_name = $data_session['bank_name'];    
    $bank_amount = $data_session['bank_amount'];
    $bank_number = $data_session['bank_number'];
    $bank_pemilik = $data_session['bank_pemilik'];
    
    
    $bank_total = $bank_amount+$bank_fee+$bank_profit;
    
    $pin_lock = filter($_POST['pin']);
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!$pin_lock) {
        ShennXit(['type' => false,'message' => 'The PIN must contain.']);
    } else if(shenn_rl_pin_gate($data_user['username']) == false) {
        ShennXit(['type' => false,'message' => 'Too many incorrect PIN attempts. Please try again in a few minutes.']);
    } else if(check_bcrypt($pin_lock,$data_user['secure']) == false) {
        shenn_rl_pin_fail($data_user['username']);
        ShennXit(['type' => false,'message' => 'Invalid Security PIN.']);       
    } else if($call->query("SELECT code FROM srv_bank WHERE code = '$bank_code'")->num_rows == 0) {
        ShennXit(['type' => false,'message' => 'Bank not found.']);
    } else if($data_user['balance'] < $bank_total) {
        ShennXit(['type' => false,'message' => 'Your balance is insufficient.']);
    } else if($data_user['balance'] < ($bank_total+$_CONFIG['hold'][$data_user['level']])) {
        ShennXit(['type' => false,'message' => 'Your minimum account balance is '.number_format($_CONFIG['hold'][$data_user['level']]).'.']);   
    } else {
        $post_wid = date('YmdHis').random_number(2);
        $prov = $call->query("SELECT * FROM provider WHERE code = 'ATL'")->fetch_assoc();
        $connect = $curl->connectPost($prov['link'].'transfer/create',['api_key' => $prov['apikey'],'ref_id' => $post_wid,'kode_bank' => $bank_code,'nomor_akun' => $bank_number,'nama_pemilik' => $bank_pemilik,'nominal' => $bank_amount,'email' => $data_user['email'],'phone' => $data_user['phone'],'note' => 'Transfer!']);
        if(isset($connect['status'])) {
	    if($connect['status'] == false) {
	        ShennXit(['type' => false,'message' => $connect['message']]);
	    } else {	        
	        $note = 'Transfer Bank :: IDR '.currency($bank_amount);
	        $tid = $connect['data']['id'];
	        $fee_server = $connect['data']['fee'];
	        $update_profit = $bank_fee-$bank_profit;
	        if($call->query("INSERT INTO trx_ppob VALUES (null,'$post_wid','$tid','$sess_username','$bank_code','$bank_name','$bank_number | $bank_pemilik','$note','$bank_total','0','$update_profit','success','0','0','WEB,$client_ip','prepaid','$date $time','$date $time','ATL')") == true) {
	            $call->query("INSERT INTO mutation VALUES (null,'$sess_username','-','$bank_total','$note','$date $time')");
                    $call->query("UPDATE users SET balance = balance-$bank_total WHERE username = '$sess_username'");
	            unset($_SESSION['LANN_SESSION']);
	            ShennXit(['type' => true,'message' => 'Successfully made a bank transfer.'],base_url('order/bank-transfer'));	
	        } else {
	            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
	        }
	    }
	} else {
	    ShennXit(['type' => false,'message' => 'Server mengalami masalah, silakan coba lagi nanti.']);
	}
    }   
}


require _DIR_('library/layout/header.user');
?>
<link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
<div class="row">
    <div class="col-12 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Account Detail</h4><br>
                <p>This is the payment menu for banking.</p>
            </div>
            <div class="card-body">                
                <ul class="list-group">
                    <li class="list-group-item d-flex">
                       <p class="float-left mb-0">
                           <i class="feather icon-credit-card mr-1"></i>
                       </p>
                       <span><?= $_SESSION['LANN_SESSION']['bank_name'] ?></b></span>
                    </li>
                    <li class="list-group-item d-flex">
                       <p class="float-left mb-0">
                           <i class="feather icon-credit-card mr-1"></i>
                       </p>
                       <span><?= $_SESSION['LANN_SESSION']['bank_number'] ?></b></span>
                    </li>
                    <li class="list-group-item d-flex">
                        <p class="float-left mb-0">
                            <i class="feather icon-credit-card mr-1"></i>
                        </p>
                        <span><?= $_SESSION['LANN_SESSION']['bank_pemilik'] ?></span>
                    </li>
                    <li class="list-group-item d-flex">
                        <p class="float-left mb-0">
                            <i class="feather icon-arrow-right-circle mr-1"></i>
                        </p>
                        <span>IDR <?= currency($_SESSION['LANN_SESSION']['bank_amount']) ?></span>
                    </li>
                    <li class="list-group-item d-flex">
                        <p class="float-left mb-0">
                            <i class="feather icon-arrow-right-circle mr-1"></i>
                        </p>
                        <span>IDR <?= currency($bank_fee+$bank_profit) ?> (Fee)</span>
                    </li>
                    <li class="list-group-item d-flex">
                        <p class="float-left mb-0">
                            <i class="feather icon-arrow-right-circle mr-1"></i>
                        </p>
                        <span>IDR <?= currency($_SESSION['LANN_SESSION']['bank_amount']+$bank_fee+$bank_profit) ?> (Total)</span>
                    </li>
                    <li class="list-group-item d-flex">                       
                        <input type="password" inputmode="numeric" maxlength="6" class="form-control" id="pin" placeholder="PIN/2FA">
                    </li>                                                                
                </ul><br>
                <form method="POST">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">  
                    <input type="hidden" class="form-control" name="pin" id="output_pin">                 
                    <button type="submit" name="cancel" class="btn btn-danger waves-effect waves-light col-12 mb-1">Cancel</button>
                    <button type="submit" name="transfer" class="btn btn-primary waves-effect waves-light col-12">Transfer</button>
            </div>
        </div>
    </div>
</div>
<?php require _DIR_('library/layout/footer.user'); ?>
<script>
$(document).ready(function() {
    $("#pin").keyup(function() {
        var x = $("#pin").val();
        $("#output_pin").val(x);
    });
});
</script>