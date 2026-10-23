<?php
require '../../connect.php';
require _DIR_('library/session/session');

$get_id = (isset($_GET['id']) && ctype_digit((string)$_GET['id'])) ? filter($_GET['id']) : '';
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($data_user)) {
    if(!isset($_SESSION['user'])) exit("No direct script access allowed!");
    if($data_user['level'] !== 'Admin') exit("No direct script access allowed!");
    $search = $call->query("SELECT * FROM srv_ppob WHERE id = '$get_id'");
    if($search->num_rows == 0) exit("No data found from the Code!");
    $row = $search->fetch_assoc();
?>
<form method="POST">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
    <input type="hidden" id="web_token" name="web_token" value="<?= base64_encode($get_id) ?>">
    <div class="row">
        <div class="form-group col-7">
            <label>Provider</label>
            <select class="form-control" name="provider_name">
                <?php
                $s = $call->query("SELECT code, name FROM provider ORDER BY name ASC");
                while($r = $s->fetch_assoc()) {
                    print select_opt($row['provider'], $r['code'], $r['name']);
                }
                ?>
            </select>
        </div>
        <div class="form-group col-5">
            <label>Code</label>
            <input type="text" class="form-control" name="provider_id" value="<?= $row['code'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label>Type</label>
        <select class="form-control" name="type" id="type">
            <?= select_opt($row['type'], 'pascabayar', ' Pascabayar'); ?>
            <?= select_opt($row['type'], 'pulsa-reguler', ' Pulsa Reguler'); ?>
            <?= select_opt($row['type'], 'pulsa-transfer', ' Pulsa Transfer'); ?>
            <?= select_opt($row['type'], 'pulsa-internasional', ' Pulsa Internasional'); ?>
            <?= select_opt($row['type'], 'paket-internet', ' Paket Internet'); ?>
            <?= select_opt($row['type'], 'paket-telepon', ' Paket Telepon dan SMS'); ?>
            <?= select_opt($row['type'], 'token-pln', ' Token Listrik (PLN)'); ?>
            <?= select_opt($row['type'], 'saldo-emoney', ' Saldo E-Money'); ?>
            <?= select_opt($row['type'], 'voucher-game', ' Voucher Game'); ?>
            <?= select_opt($row['type'], 'streaming-tv', ' Streaming & TV'); ?>
            <?= select_opt($row['type'], 'paket-lainnya', ' Kategori Lainnya'); ?>
            <?= select_opt($row['type'], 'akun-premium', ' Akun Premium'); ?>
            <?= select_opt($row['type'], 'masa-aktif', ' Masa Aktif'); ?>
        </select>
    </div>
    <div class="form-group">
        <label>Brand</label>
        <select class="form-control" name="category" id="category">
            <?php
            $s = $call->query("SELECT code, name FROM category WHERE type = '".$row['type']."' AND `order` IN ('prepaid', 'postpaid') ORDER BY name ASC");
            while($r = $s->fetch_assoc()) {
                print select_opt($row['brand'], $r['code'], $r['name']);
            }
            ?>
        </select>
        <small class="danger">*Now: <?= $row['brand'] ?></small>
    </div>
    <div class="form-group">
        <label>Name</label>
        <input type="text" class="form-control" name="service" value="<?= $row['name'] ?>">
    </div>
    <div class="form-group">
        <label>Note</label>
        <textarea class="form-control" rows="<?= count(explode("\n", $row['note'])) ?>" name="description"><?= $row['note'] ?></textarea>
    </div>
    <div class="form-group">
        <label>Price</label>
        <input type="number" class="form-control" name="price" value="<?= $row['price'] ?>">
    </div>
    <div class="row">
        <div class="form-group col-6">
            <label>Profit Basic</label>
            <input type="number" class="form-control" name="pbasic" value="<?= $row['basic'] ?>">
        </div>
        <div class="form-group col-6">
            <label>Profit Premium</label>
            <input type="number" class="form-control" name="ppremi" value="<?= $row['premium'] ?>">
        </div>
    </div>
    <div class="form-group">
        <label>Status</label>
        <select class="form-control" name="status">
            <?= select_opt($row['status'], 'empty', ' Empty '); ?>
            <?= select_opt($row['status'], 'available', ' Available '); ?>
        </select>
    </div>
    <hr>
    <div class="row">
        <div class="form-group col-6">
            <button type="button" name="reset" class="btn btn-danger btn-block" data-dismiss="modal"> BACK </button>
        </div>
        <div class="form-group col-6">
            <button type="submit" name="servicep_edit" class="btn btn-primary btn-block"> EDIT </button>
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