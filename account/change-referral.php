<?php
require '../connect.php';
require _DIR_('library/session/session');

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($data_user)) {
    if(!isset($_SESSION['user'])) exit("No direct script access allowed!");
    $equalReferral = ($json_user['old_referral'] == $json_user['new_referral']) ? true : false;
    require _DIR_('library/shennboku.app/account-change-referral');
?>
<div id="sess-result-modal"></div>
<form method="POST" id="form-ce">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
    <div class="form-group">
        <label>Old Referral</label>
        <input type="text" class="form-control" value="<?= $data_user['referral'] ?>" disabled>
    </div>
    <div class="form-group">
        <label>New Referral</label>
        <div class="input-group">
            <div class="input-group-append">
                <?php if($equalReferral == false) { ?>
                <button type="button" onclick="ShennForm('#form-ce','<?= base_url('account/change-referral?cancel=1') ?>');" class="btn btn-danger waves-effect waves-light">
                    <i class="feather icon-x"></i>
                </button>
                <?php } ?>
            </div>
            <input type="referral" class="form-control" name="u_referral" value="<?= ($equalReferral == true) ? '' : $json_user['new_referral'] ?>" <?= ($equalReferral == true) ? 'required' : 'disabled' ?> data-validation-required-message="This mail field is required">
            <div class="input-group-append">
                <?php if($equalReferral == true) { ?>
                <button type="button" onclick="ShennForm('#form-ce','<?= base_url('account/change-referral?send=1') ?>');" class="btn btn-primary waves-effect waves-light">
                    Send
                </button>
                <?php } else { ?>
                <button type="button" onclick="ShennForm('#form-ce','<?= base_url('account/change-referral?resend=1') ?>');" class="btn btn-primary waves-effect waves-light">
                    Resend
                </button>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Confirmation Code</label>
        <input type="text" class="form-control" name="c_referral" required data-validation-required-message="This confirmation field is required" minlength="0" maxlength="12">
    </div>
    <hr>
    <div class="row">
        <div class="form-group col-6">
            <button type="button" name="reset" class="btn btn-danger btn-block" data-dismiss="modal"> BACK </button>
        </div>
        <div class="form-group col-6">
            <button type="button" onclick="ShennForm('#form-ce','<?= base_url('account/change-referral?confirm=1') ?>');" class="btn btn-primary btn-block"> CONFIRM </button>
        </div>
    </div>
</form>
<?php
} else {
    exit("No direct script access allowed!");
}
?>