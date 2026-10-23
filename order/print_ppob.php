<?php
require '../connect.php';
require _DIR_('library/session/user');
$get_wid = isset($_GET['wid']) ? filter($_GET['wid']) : '';
$get_price = isset($_GET['price']) ? filter($_GET['price']) : '';
$s = $call->query("SELECT * FROM trx_ppob WHERE wid = '$get_wid' AND user = '$sess_username'");
if($s->num_rows == 0) redirect(0,base_url('order/history/pulsa-ppob'));
$r = $s->fetch_assoc();
$date = new DateTime($r['date_up']);
$formattedDate = $date->format('d F Y');
$formattedTime = $date->format('H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CETAK STRUK</title>

  <style>
    body {
      padding: 0;
      margin: 0;
      font-family: monospace;
    }
    table {
      width: 100%;
      margin-bottom: 10px;
    }
    table th {
      text-align: left;
    }
    table th, table td {
      vertical-align: top;
    }
    h1, p {
      margin: 0;
    }
    .header {
      margin-bottom: 10px;
    }
    .container {
      max-width: 52mm;
    }
    .text-right {
      text-align: right;
    }
    .text-center {
      text-align: center;
    }
    .detail, .info tr:nth-child(2) td {
      border-top: 1px dashed #000;
    }
    .info tr:nth-child(2) td {
      padding-top: 10px;
    }
    .detail tbody td, .detail {
      border-bottom: 1px dashed #000;
      padding-top: 7px;
      padding-bottom: 10px;
    }
    .info tbody td, .detail tfoot th {
      padding-bottom: 5px;
    }
    .qty {
      margin-top: 5px;
    }
    .padding {
      padding: 5px;
    }
    .thanks {
      text-align: center;
      margin-top: 30px;
    }
    .tanggal {
      text-align: center;
      
    }

@page {
      margin: 5px;
    }
  </style>
</head>
<body>
  <div class="container">
    <table class="info">
      <tr>
        <td colspan="3">
          <div class="text-center header">
            <!--<h1><?=  $_CONFIG['navbar']?></h1>-->
            <h1>BUKTI TRANSAKSI</h1>
            <p>
              <h2><?= $json_user['store']['name'] ?></h2>
            </p>
          </div>
        </td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td class="tanggal">
  <b><i><?= $formattedDate ?>,<br> <?= $formattedTime ?> WIB</i></b>
 </td>
      </tr><br>
      
      <tr>
        <td>ID TRANSAKSI</td>
        <td>:</td>
        <td><?= $r['wid'] ?></td>
      </tr>
      <tr>
        <td>Pesanan</td>
        <td>:</td>
        <td><?= $r['name'] ?></td>
      </tr>
      <tr>
        <td>Tujuan</td>
        <td>:</td>
        <td><?= $r['data'] ?></td>
      </tr>
      <tr>
        <td>SN/Ket</td>
        <td>:</td>
        <td><?= $r['note'] ?></td>
      </tr>
      <tr>
        <td>Status</td>
        <td>:</td>
        <td><b><?= $r['status'] ?></b></td>
      </tr>
    </table>
    <table class="detail">
      <tfoot>
        <tr>
          <th>Harga</th>
          <th class="text-right">
            <?= currency($get_price) ?>                       
          </th>
        </tr>
      </tfoot>
    </table>
    <div class="thanks">
      Terima Kasih
    </div>
  </div>
  <script src="<?= assets('struk/jquery.min.js') ?>"></script>
  <script>
    if ($(window).width() > 768) {
      setTimeout(function () {
        window.print();
      }, 0);
      window.onload = function () {
        setTimeout(function () {
          window.close();
        }, 0);
      }
    } else {
      print();
    }
  </script>
</body>
</html>