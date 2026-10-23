<?php
require '../../connect.php';
require _DIR_('library/session/user');

$get_wid = isset($_GET['wid']) ? filter($_GET['wid']) : '';
$search = $call->query("SELECT * FROM trx_ppob WHERE wid = '$get_wid'");
if($search->num_rows == 0) redirect(0,base_url('order/history/pulsa-ppob'));
$row = $search->fetch_assoc();
    if($row['status'] == 'error') $get_clr = 'text-danger';
    if($row['status'] == 'waiting') $get_clr = 'text-warning';
    if($row['status'] == 'success') $get_clr = 'text-success';    
    $get_ic .= ucfirst($row['status']);
    if($row['refund'] == '1') $get_ic .= ' - Refunded!';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="<?= $_CONFIG['description']; ?>" name="description">
    <meta content="<?= $_CONFIG['keyword']; ?>" name="keyword">
    <meta name="author" content="Lann Digital Solutions">
  
    <title><?= $_CONFIG['navbar'].' | Detail Transaction' ?></title>
    
    <meta name="robots" content="<?= $_META['robots'] ?>">
    <meta name="revisit-after" content="<?= $_META['revisit'] ?>">
    <meta name="msvalidate.01" content="<?= $_META['bing_site'] ?>">
    <meta name="google-site-verification" content="<?= $_META['google_site'] ?>">
        
    <meta name="Geo.Placename" content="<?= $_META['geo_placename'] ?>">
    <meta name="Geo.Country" content="<?= $_META['geo_country'] ?>">
    
    <meta property="og:title" content="<?= $_CONFIG['title'] ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= base_url() ?>">
    <meta property="og:image" content="<?= $_CONFIG['banner'] ?>">
    <meta property="og:image:type" content="image/png">
    <meta property="og:description" content="<?= $_CONFIG['description'] ?>">
    <meta property="og:site_name" content="<?= $_CONFIG['title'] ?>">
    <meta property="og:locale" content="id_ID">
    
    <meta name="apple-mobile-web-app-title" content="<?= $_CONFIG['title']; ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    
    <link rel="apple-touch-icon" href="<?= $_CONFIG['icon']; ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= $_CONFIG['icon']; ?>">
    
    <!-- Custom Meta -->
    <meta name="DeviceType" content="<?= $device ?>">
    <meta name="UserAgent" content="<?= $user_agent ?>">
    <meta name="IpAddress" content="<?= client_ip() ?>">
    <meta name="Rating" content="General">
    
    <!-- Bosstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: "Poppins";
            letter-spacing: 0.5px;
        }
        
        .font-size-2-5-rem {
            font-size: 2.5rem;
        }

        .font-size-1-5-rem {
            font-size: 1.5rem;
        }

        .d-trx {
            margin-left: 10px;
            font-weight: 700;
        }

        .transaksi {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-left: 10px;
            text-align: left;
            font-weight: 300;
        }

        .font-size-14 {
            font-size: 14px;
        }

        .sn_ket {
            margin-left: 10px;
            display: flex;
            align-items: center;
            justift-content: center;
        }

        .sn_ket input {
            width: 300px;
            padding: 10px 15px;
        }

        .desc {
            margin-left: 10px;
            font-weight: 300;
            margin-top: 10px;
            font-size: 14px;
            letter-spacing: 0.7px;
        }
    </style><?php require _DIR_('library/custom.js'); ?>  
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>       
</head>
<body>
    <nav class="navbar bg-white">
        <div class="container-fluid">
            <a class="navbar-brand font-size-2-5-rem" href="<?= base_url('order/history/pulsa-ppob') ?>"><i class="bi bi-x"></i></a>
            <a class="navbar-brand font-size-1-5-rem"><i class="bi bi-printer"></i></a>
        </div>
    </nav>
    <div class="container-fluid">
        <h4 class="d-trx mb-3">Detail Transaksi</h4>
        <div class="transaksi">
            <div class="row">
                <div class="col-6 mb-3">
                    <span>ID Transaksi</span>
                </div>
                <div class="col-6">
                    <span class="font-size-14"><?= $get_wid ?></span>
                </div>
                <div class="col-6 mb-3">
                    <span>Status</span>
                </div>
                <div class="col-6">
                    <span class="<?= $get_clr ?> fw-bold font-size-14"><?= $get_ic ?></span>
                </div>
                <div class="col-6 mb-3">
                    <span>Layanan</span>
                </div>
                <div class="col-6">
                    <span class="font-size-14"><?= $row['name'] ?></span>
                </div>
                <?php if($row['status'] !== 'error') { ?>
                <div class="col-6 mb-3">
                    <span>Harga</span>
                </div>
                <div class="col-6">
                    <span class="font-size-14">Rp <?= currency($row['price']) ?></span>
                </div>
                <?php } ?>
                <div class="col-6 mb-3">
                    <span>Tujuan</span>
                </div>
                <div class="col-6">
                    <span class="font-size-14"><?= $row['data'] ?></span>
                </div>
                <div class="col-6 mb-3">
                    <span>Tanggal</span>
                </div>
                <div class="col-6">
                    <span class="font-size-14"><?= date('d F Y, H.i', strtotime($row['date_cr'])) ?></span>
                </div>
                <div class="col-6 mb-3">
                    <span>Dari</span>
                </div>
                <div class="col-6">
                    <span class="font-size-14"><?= $row['trxfrom'] ?></span>
                </div>
            </div>
        </div>
        <?php if($row['status'] == 'success' || $row['status'] == 'error') { ?>
        <div class="sn_ket">
            <input type="text" class="lann_input" value="<?= $row['note'] ?>" disabled>
        </div>
        <?php } ?>       
        <div class="desc">
            Transaksi ini sah secara Legal dan sudah di awasi undang-undang, apabila Anda melakukan kecurangan pada transaksi ini, syarat dan ketentuan hak menuntut secara hukum.
        </div>
        <img src="<?= assets('images/image.png') ?>" width="150" style="margin-left: 10px; margin-top: 20px;background: #eee">
    </div>

    <!-- Boostrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
<script type="text/javascript">
$(document).ready(function() {
    setInterval(() => {
        $.ajax({
            url: '<?= ajaxlib('status-loaded') ?>',
            data: 'hash=<?= base64_encode($get_wid) ?>&csrf_token='+ csrf_key,
            type: 'POST',
            dataType: 'json',
            success: function(x) {
                if(x.result == true) {
                    if(x.status != '<?= $row['status'] ?>')
                    location.href = '<?= base_url('detail/transaction/'.$get_wid) ?>';
                }
            }
        });
    }, 2000);
});
</script>