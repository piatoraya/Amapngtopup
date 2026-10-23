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
    
    $data_provider = $call->query("SELECT * FROM provider WHERE code = '".$row['provider']."'");
    if($data_provider->num_rows == 1) $data_provider = $data_provider->fetch_assoc();
    $nama_provider = (is_array($data_provider)) ? $data_provider['name'] : $row['provider'];
    
    $out_status = '<font class="text-danger">Out of Stock!</font>';
    if($row['status'] == 'available') $out_status = '<font class="text-success">Available!</font>';

    $data_category = $call->query("SELECT * FROM category WHERE code = '".$row['brand']."' AND `type` = '".$row['type']."' AND `order` IN ('prepaid','postpaid')");
    if($data_category->num_rows == 1) $data_category = $data_category->fetch_assoc();
    $nama_category = (is_array($data_category)) ? $data_category['name'] : $row['brand'];
    $nama_type = strtr($row['type'],[
        'pulsa-reguler'       => 'Pulsa Reguler',           'voucher-game'  => 'Voucher Game',
        'pulsa-internasional' => 'Pulsa Internasional',     'streaming-tv'  => 'Streaming & TV',
        'paket-internet'      => 'Paket Internet',          'saldo-emoney'  => 'Saldo E-Money',
        'paket-telepon'       => 'Paket Telepon dan SMS',   'paket-lainnya' => 'Kategori Lainnya',
        'token-pln'           => 'Token Listrik (PLN)',     'pascabayar'    => 'Pascabayar',
        'akun-premium'        => 'Akun Premium',            'masa-aktif'    => 'Masa Aktif',
    ]);
?>
<form method="POST">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
    <input type="hidden" id="web_token" name="web_token" value="<?= base64_encode($row['id']) ?>">
    <table style="font-size:0.8rem;border-collapse:separate;border-spacing:1px;">
        <tbody>
            <tr>
                <td>Type</td>
                <td>:</td>
                <td><?= $nama_type ?></td>
            </tr>
            <tr>
                <td>Code</td>
                <td>:</td>
                <td><?= $row['code'] ?></td>
            </tr>
            <tr>
                <td>Brand</td>
                <td>:</td>
                <td><?= $nama_category ?></td>
            </tr>
            <tr>
                <td>Name</td>
                <td>:</td>
                <td><?= $row['name'] ?></td>
            </tr>
            <tr>
                <td>Status</td>
                <td>:</td>
                <td><?= $out_status ?></td>
            </tr>
            <tr>
                <td>Provider</td>
                <td>:</td>
                <td><?= $nama_provider ?></td>
            </tr>
        </tbody>
    </table>
    <hr>
    <div class="row">
        <div class="form-group col-6">
            <button type="button" class="btn btn-danger btn-block" data-dismiss="modal"> BACK </button>
        </div>
        <div class="form-group col-6">
            <button type="submit" name="servicep_delete" class="btn btn-primary btn-block"> DELETE </button>
        </div>
    </div>
</form>
<?php
} else {
    exit("No direct script access allowed!");
}
?>