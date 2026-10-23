<?php
require '../../connect.php';
require _DIR_('library/session/admin');

// CRON DEPOSIT
// https://domainkamu.com/library/cron/benefit/deposit?key=<SHENN_CRON_KEY>

// CRON TRANSAKSI
// https://domainkamu.com/library/cron/benefit/transaksi?key=<SHENN_CRON_KEY>


if(isset($_POST['saved'])) {
    $post_nominal = filter($_POST['d_nominal']);
    $post_benefit = filter($_POST['d_benefit']);
    $post_periode = filter($_POST['d_periode']);
    $post_jangka_keuntungan = filter($_POST['d_jangka_keuntungan']);
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
    } else if(!is_numeric($post_nominal)) {
        ShennXit(['type' => false,'message' => 'The nominal must contain numbers.']);
    } else if(!in_array($post_benefit,['Premium','Special'])) {
        ShennXit(['type' => false,'message' => 'Invalid keuntungan.']);
    } else if(!is_numeric($post_periode)) {
        ShennXit(['type' => false,'message' => 'The periode must contain numbers.']);
    } else if(!is_numeric($post_jangka_keuntungan)) {
        ShennXit(['type' => false,'message' => 'The periode benefit must contain numbers.']);
    } else {
        if($call->query("UPDATE conf SET c1 = '$post_nominal', c2 = '$post_benefit', c3 = '$post_periode', c4 = '$post_jangka_keuntungan' WHERE code = 'benefit-deposit'") == true) {
            ShennXit(['type' => true,'message' => 'Benefit Deposit successfully updated.']);
        } else {
            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
        }
    }  
}

if(isset($_POST['savet'])) {
    $post_nominal = filter($_POST['t_nominal']);
    $post_benefit = filter($_POST['t_benefit']);
    $post_periode = filter($_POST['t_periode']);
    $post_jangka_keuntungan = filter($_POST['t_jangka_keuntungan']);
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
    } else if(!is_numeric($post_nominal)) {
        ShennXit(['type' => false,'message' => 'The nominal must contain numbers.']);
    } else if(!in_array($post_benefit,['Premium','Special'])) {
        ShennXit(['type' => false,'message' => 'Invalid keuntungan.']);
    } else if(!is_numeric($post_periode)) {
        ShennXit(['type' => false,'message' => 'The periode must contain numbers.']);
    } else if(!is_numeric($post_jangka_keuntungan)) {
        ShennXit(['type' => false,'message' => 'The periode benefit must contain numbers.']);
    } else {
        if($call->query("UPDATE conf SET c1 = '$post_nominal', c2 = '$post_benefit', c3 = '$post_periode', c4 = '$post_jangka_keuntungan' WHERE code = 'benefit-transaksi'") == true) {
            ShennXit(['type' => true,'message' => 'Benefit Transaksi successfully updated.']);
        } else {
            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
        }
    }  
}

require _DIR_('library/layout/header.admin');
?>
<div class="row">
    <div class="col-12 col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Benefit</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
                    
                    <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                       <li class="nav-item">
                           <a class="nav-link active" id="deposit-tab-fill" data-toggle="tab" href="#deposit" role="tab" aria-controls="detail" aria-selected="false">Deposit</a>
                       </li>
                       <li class="nav-item">
                           <a class="nav-link" id="transaksi-tab-fill" data-toggle="tab" href="#transaksi" role="tab" aria-controls="detail" aria-selected="false">Transaksi</a>
                       </li>
                    </ul>
                    
                    <div class="tab-content">
                        <div class="tab-pane show active" id="deposit">
                            <div class="form-group">
                                <label>Nominal *</label>
                                <input type="number" name="d_nominal" pettern="[0-9]" class="form-control" value="<?= conf('benefit-deposit',1) ?>">
                            </div>
                            <div class="form-group">
                                <label>Keuntungan *</label>
                                <select class="form-control" name="d_benefit">
                                    <?= select_opt(conf('benefit-deposit',2),'Premium','Premium') ?>
                                    <?= select_opt(conf('benefit-deposit',2),'Special','Special') ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jangka (Hari) *</label>
                                <input type="number" name="d_periode" pettern="[0-9]" class="form-control" value="<?= conf('benefit-deposit',3) ?>">
                            </div>
                            <div class="form-group">
                                <label>Jangka keuntungan (Hari) *</label>
                                <input type="number" name="d_jangka_keuntungan" petern="[0-9]" class="form-control" value="<?= conf('benefit-deposit',4) ?>">
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                     <button type="submit" name="reset" class="btn btn-danger waves-effect waves-light col-12">Reset</button>
                                </div>
                                <div class="form-group col-12">
                                     <button type="submit" name="saved" class="btn btn-primary waves-effect waves-light col-12">Submit</button>
                                </div>                       
                            </div>                      
                        </div>
                        <div class="tab-pane" id="transaksi">
                            <div class="tab-pane show active" id="transaksi">
                            <div class="form-group">
                                <label>Nominal *</label>
                                <input type="number" name="t_nominal" class="form-control" pettern="[0-9]" value="<?= conf('benefit-transaksi',1) ?>">
                            </div>
                            <div class="form-group">
                                <label>Keuntungan *</label>
                                <select name="t_benefit" class="form-control">
                                    <?= select_opt(conf('benefit-transaksi',2),'Premium','Premium') ?>
                                    <?= select_opt(conf('benefit-transaksi',2),'Special','Special') ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jangka (Hari) *</label>
                                <input type="number" name="t_periode" petern="[0-9]" class="form-control" value="<?= conf('benefit-transaksi',3) ?>">
                            </div>
                            <div class="form-group">
                                <label>Jangka keuntungan (Hari) *</label>
                                <input type="number" name="t_jangka_keuntungan" petern="[0-9]" class="form-control" value="<?= conf('benefit-transaksi',4) ?>">
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                     <button type="submit" name="reset" class="btn btn-danger waves-effect waves-light col-12">Reset</button>
                                </div>
                                <div class="form-group col-12">
                                     <button type="submit" name="savet" class="btn btn-primary waves-effect waves-light col-12">Submit</button>
                                </div>                       
                            </div>
                        </div>
                    </div>             
                </form>
            </div>
        </div>
    </div>
</div>
<?php require _DIR_('library/layout/footer.user'); ?>