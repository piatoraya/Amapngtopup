<?php
require '../../connect.php';
require _DIR_('library/session/session');

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($data_user)) {
    if(!isset($_SESSION['user'])) exit("No direct script access allowed!");
    if($data_user['level'] !== 'Admin') exit("No direct script access allowed!");
?>
<form method="POST">
    <input type="hidden" id="web_token" name="web_token" value="<?= base64_encode('ppob') ?>">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
    <div class="form-group">
        <label>Option</label>
        <select class="form-control" name="clear_option" id="clear_option">
            <option value="0" selected disabled>- Select One -</option>
            <optgroup label="Single Option">
                <option value="type">Type</option>
                <option value="category">Category</option>
                <option value="provider">Provider</option>
            </optgroup>
            <optgroup label="Double">
                <option value="type-provider">Type & Provider</option>
                <option value="category-provider">Category & Provider</option>
            </optgroup>
        </select>
    </div>
    <div class="form-group d-none" id="seltype">
        <label>Choose Type</label>
        <select class="form-control" name="clear_type">
            <option value="0" selected disabled>- Select Type -</option>
            <option value="pulsa-reguler">
                [<?= currency(squery("SELECT id FROM category WHERE `type` = 'pulsa-reguler'")->num_rows) ?>]
                Pulsa Reguler
            </option>
            <option value="pulsa-internasional">
                [<?= currency(squery("SELECT id FROM category WHERE `type` = 'pulsa-internasional'")->num_rows) ?>]
                Pulsa Internasional
            </option>
            <option value="paket-internet">
                [<?= currency(squery("SELECT id FROM category WHERE `type` = 'paket-internet'")->num_rows) ?>]
                Paket Internet
            </option>
            <option value="paket-telepon">
                [<?= currency(squery("SELECT id FROM category WHERE `type` = 'paket-telepon'")->num_rows) ?>]
                Paket Telepon dan SMS
            </option>
            <option value="token-pln">
                [<?= currency(squery("SELECT id FROM category WHERE `type` = 'token-pln'")->num_rows) ?>]
                Token Listrik (PLN)
            </option>
            <option value="saldo-emoney">
                [<?= currency(squery("SELECT id FROM category WHERE `type` = 'saldo-emoney'")->num_rows) ?>]
                Saldo E-Money
            </option>
            <option value="voucher-game">
                [<?= currency(squery("SELECT id FROM category WHERE `type` = 'voucher-game'")->num_rows) ?>]
                Voucher Game
            </option>
            <option value="streaming-tv">
                [<?= currency(squery("SELECT id FROM category WHERE `type` = 'streaming-tv'")->num_rows) ?>]
                Streaming & TV
            </option>
            <option value="paket-lainnya">
                [<?= currency(squery("SELECT id FROM category WHERE `type` = 'paket-lainnya'")->num_rows) ?>]
                Kategori Lainnya
            </option>
            <option value="akun-premium">
                [<?= currency(squery("SELECT id FROM category WHERE `type` = 'akun-premium'")->num_rows) ?>]
                Akun Premium
            </option>
            <option value="masa-aktif">
                [<?= currency(squery("SELECT id FROM category WHERE `type` = 'masa-aktif'")->num_rows) ?>]
                Masa Aktif
            </option>
        </select>
    </div>
    <div class="form-group d-none" id="selcategory">
        <label>Choose Category</label>
        <select class="form-control" name="clear_category">
            <option value="0" selected disabled>- Select Category -</option>
            <?php
            $s = $call->query("SELECT * FROM category WHERE `order` IN ('prepaid', 'postpaid')");
            while($r = $s->fetch_assoc()) {
                print '<option value="'.$r['code'].'">['.currency(squery("SELECT id FROM srv_ppob WHERE brand = '".$r['code']."'")->num_rows).'] '.$r['name'].'</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group d-none" id="selprovider">
        <label>Choose Provider</label>
        <select class="form-control" name="clear_provider">
            <option value="0" selected disabled>- Select Provider -</option>
            <?php
            $s = $call->query("SELECT * FROM provider");
            while($r = $s->fetch_assoc()) {
                print '<option value="'.$r['code'].'">['.currency(squery("SELECT id FROM srv_ppob WHERE provider = '".$r['code']."'")->num_rows).'] '.$r['name'].'</option>';
            }
            ?>
        </select>
    </div>
    <hr>
    <div class="row">
        <div class="form-group col-6">
            <button type="button" name="reset" class="btn btn-danger btn-block" data-dismiss="modal"> BACK </button>
        </div>
        <div class="form-group col-6">
            <button type="submit" name="service_clear" class="btn btn-primary btn-block"> CLEAR </button>
        </div>
    </div>
</form>
<script type="text/javascript">
$(document).ready(function() {
    $("#clear_option").change(function() {
        var select = $("#clear_option").val();
        $('#seltype').addClass('d-none');
        $('#selcategory').addClass('d-none');
        $('#selprovider').addClass('d-none');
        
        if(select == 'type' || select == 'type-provider') $('#seltype').removeClass('d-none');
        if(select == 'category' || select == 'category-provider') $('#selcategory').removeClass('d-none');
        if(select == 'provider' || select == 'type-provider' || select == 'category-provider') $('#selprovider').removeClass('d-none');
    });
});
</script>
<?php
} else {
    exit("No direct script access allowed!");
}
?>