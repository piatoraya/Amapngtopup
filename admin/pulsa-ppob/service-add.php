<?php
require '../../connect.php';
require _DIR_('library/session/session');

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($data_user)) {
    if(!isset($_SESSION['user'])) exit("No direct script access allowed!");
    if($data_user['level'] !== 'Admin') exit("No direct script access allowed!");
?>
<form method="POST">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
    <div class="row">
        <div class="form-group col-7">
            <label>Provider</label>
            <select class="form-control" name="provider_name">
                <option value=""> - Select One - </option>
                <?php
                $search = $call->query("SELECT code, name FROM provider ORDER BY name ASC");
                while($row = $search->fetch_assoc()) {
                    print '<option value="'.$row['code'].'">'.$row['name'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="form-group col-5">
            <label>Code</label>
            <input type="text" class="form-control" name="provider_id">
        </div>
    </div>
    <div class="form-group">
        <label>Type</label>
        <select class="form-control" name="type" id="type">
            <option value="0" selected disabled>- Select One -</option>
            <option value="pascabayar">Pascabayar</option>
            <option value="pulsa-reguler">Pulsa Reguler</option>
            <option value="pulsa-transfer">Pulsa Transfer</option>
            <option value="pulsa-internasional">Pulsa Internasional</option>
            <option value="paket-internet">Paket Internet</option>
            <option value="paket-telepon">Paket Telepon dan SMS</option>
            <option value="token-pln">Token Listrik (PLN)</option>
            <option value="saldo-emoney">Saldo E-Money</option>
            <option value="voucher-game">Voucher Game</option>
            <option value="streaming-tv">Streaming & TV</option>
            <option value="paket-lainnya">Kategori Lainnya</option>
            <option value="akun-premium">Akun Premium</option>
            <option value="masa-aktif">Masa Aktif</option>
        </select>
    </div>
    <div class="form-group">
        <label>Brand</label>
        <select class="form-control" name="category" id="category">
            <option value=""> - Select One - </option>
        </select>
    </div>
    <div class="form-group">
        <label>Name</label>
        <input type="text" class="form-control" name="service">
    </div>
    <div class="form-group">
        <label>Note</label>
        <textarea class="form-control" name="description"></textarea>
    </div>
    <div class="form-group">
        <label>Price</label>
        <input type="number" class="form-control" name="price">
    </div>
    <div class="row">
        <div class="form-group col-6">
            <label>Profit Basic</label>
            <input type="number" class="form-control" name="pbasic">
        </div>
        <div class="form-group col-6">
            <label>Profit Premium</label>
            <input type="number" class="form-control" name="ppremi">
        </div>
    </div>
    <div class="form-group">
        <label>Status</label>
        <select class="form-control" name="status">
            <option value=""> - Select One - </option>
            <option value="empty"> Empty </option>
            <option value="available"> Available </option>
        </select>
    </div>
    <hr>
    <div class="row">
        <div class="form-group col-6">
            <button type="button" name="reset" class="btn btn-danger btn-block" data-dismiss="modal"> BACK </button>
        </div>
        <div class="form-group col-6">
            <button type="submit" name="servicep_add" class="btn btn-primary btn-block"> ADD </button>
        </div>
    </div>
</form>
<script type="text/javascript">
$(document).ready(function() {
    $("#type").change(function() {
        var type = $("#type").val();
        $.ajax({
            url: '<?= ajaxlib('category-pulsa') ?>',
            data: 'type=' + type + '&admin=1&csrf_token=<?= $csrf_string ?>',
            type: 'POST',
            dataType: 'html',
            beforeSend: function() {
                $("#category").html("<option selected disabled hidden>Please wait..</option>");
            }, success: function(msg) {
                $("#category").html(msg);
            }
        });
    });
});
</script>
<?php
} else {
    exit("No direct script access allowed!");
}
?>