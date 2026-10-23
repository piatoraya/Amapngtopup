<?php
require 'connect.php';

if(!isset($_SESSION['user'])) {
    if(isset($_COOKIE['token']) && isset($_COOKIE['ssid'])) {
        $call->query("DELETE FROM users_cookie WHERE DATEDIFF(expired,'$date') < 0");
        $ShennUID = preg_replace('/[^\d]+/i','',$_COOKIE['ssid']) + 0;
        $ShennKey = filter($_COOKIE['token']);
        $ShennSSO = sha1(sha1(strtotime(date('Y-m-d H:i:s'))));
        $ShennUser = $call->query("SELECT * FROM users WHERE id = '$ShennUID'")->fetch_assoc();
        if(is_array($ShennUser)) {
            $ShennCheck = $call->query("SELECT * FROM users_cookie WHERE cookie = '$ShennKey' AND username = '".$ShennUser['username']."'");
            if($ShennCheck->num_rows == 1) {
                if($ShennUser['level'] <> 'Admin' && $_CONFIG['mt']['web'] == 'true') {
                } else {
                    $_SESSION['sso'] = $ShennSSO;
                    $_SESSION['user'] = $ShennUser;
                    redirect(0,visited());
                    $call->query("UPDATE users SET sso = '$ShennSSO' WHERE id = ".$ShennUser['id']."");
                    $call->query("UPDATE users_cookie SET active = '$date $time', ua = '$user_agent', ip = '$client_ip' WHERE cookie = '$ShennKey'");
                }
            } else {
                exit(redirect(0,base_url('auth/logout')));
            }
        } else {
            exit(redirect(0,base_url('auth/logout')));
        }
    }
}

if(!isset($_SESSION['user'])) {
    require _DIR_('app/dexdex');
} else {
    require _DIR_('library/session/user');

    function OBX($x) {
        if(is_array($x)) {
            for($i = 0; $i <= count($x)-1; $i++) {
                $display = isset($x[$i]['display']) ? $x[$i]['display'] : '';
                if(isset($x[$i]['action']) && isset($x[$i]['image']) && isset($x[$i]['name']) && $display == 'true') {
                    $ShennOut['view'][] = base64_encode('
            <div class="_24EoF">
                <a href="javascript:;" '.onclick_href(base_url($x[$i]['action'])).'>
                    <div class="avatar avatar-40 no-shadow border-0">
                        <div class="overlay gradient-primary"><img class="img-fluid" src="'.$x[$i]['image'].'" alt="'.$x[$i]['name'].'" style="height: 30px;"></div>
                    </div><span title="'.$x[$i]['name'].'">'.$x[$i]['name'].'</span>
                </a>
            </div>');
                }
            }
            $count = count($ShennOut['view'])/4;
            $count = (stristr($count,'.')) ? 4*(explode('.',$count)[0] + 1) : 4*$count;
            for($i = 0; $i <= $count-1; $i++) print (isset($ShennOut['view'][$i])) ? base64_decode($ShennOut['view'][$i]) : '<div class="_3xM0V"></div>';
        }
    }
    
    $search_banner = $call->query("SELECT * FROM information WHERE `type` = 'banner'");
    if($search_banner->num_rows == 0) {
        $banner[] = base_url('library/assets/banner/demosample.jpeg');
    } else {
        while($row_banner = $search_banner->fetch_assoc()) {
            $banner[] = $row_banner['content'];
        }
    }    
    
    /* NEW FITUR BENEFIT TRANSAKSI */
    $trx_ppob_currency = $call->query("SELECT SUM(price) AS total FROM trx_ppob WHERE status = 'success' AND user = '$sess_username'")->fetch_assoc()['total'];
    $trx_game_currency = $call->query("SELECT SUM(price) AS total FROM trx_game WHERE status = 'success' AND user = '$sess_username'")->fetch_assoc()['total'];
    $total_currency = $trx_ppob_currency+$trx_game_currency;    
    if(conf('benefit-transaksi',1) < $total_currency) $iBenefitTrx = 'Passed';
    else $iBenefitTrx = 'Rp '.currency(conf('benefit-transaksi',1)-$total_currency);
    
    require _DIR_('library/layout/header.user');
?>
<div class="alert bg-gradient-info text-white border-0" role="alert">
            <font style="font-size: 0.9rem;"><strong>Whatsapp Channel:</strong> <?= LannREF('https://whatsapp.com/channel/0029VaZvqBNAojYvhq89le2F'); ?></font> 
            
          
            
        </div>
<div class="row">
    <div class="col-md-6 col-12">
        <div id="carouselExampleCaptions" class="carousel slide mb-0" data-ride="carousel">
            <ol class="carousel-indicators">
                <?php
                for($i = 0; $i <= count($banner)-1; $i++) {
                    $active = ($i == 0) ? ' class="active"' : '';
                    print '<li data-target="#carouselExampleCaptions" data-slide-to="'.$i.'"'.$active.'></li>';
                }
                ?>
            </ol>
            <div class="carousel-inner" style="border-radius: 10px;" role="listbox">
                <?php
                for($i = 0; $i <= count($banner)-1; $i++) {
                    $active = ($i == 0) ? ' active' : '';
                    print '<div class="carousel-item'.$active.'">';
                    print '<a href="'.$banner[$i].'" class="image-popup">';
                    print '<img class="d-block img-fluid" src="'.$banner[$i].'">';
                    print '</a></div>';
                }
                ?>
            </div>
        </div>
        <div class="container">
            <div class="kotak mb-4 shadow">
                <div class="kotak-body border-bottom">
                    <div class="row">
                        <div class="col text-right">
                            <p>Rp <?= currency($data_user['balance']) ?><br><small class="text-mute">Balance</small></p>
                        </div>
                        <div class="col border-left-dotted-blue">
                            <p>Rp <?= currency($data_user['point']) ?><br><small class="text-mute">Point</small></p>

                        </div>
                    </div>
                </div>
            </div>            
        </div>
        <div class="_1ekcz mt-1" style="width: 100%;">
<?php OBX([
    [
        'action' => 'order/pulsa-reguler',
        'image' => assets('images/menu/005-smartphone.svg'),
        'name' => 'Pulsa Reguler',
        'display' => 'true',
    ],[
        'action' => 'order/pulsa-transfer',
        'image' => assets('images/menu/013-5g.svg'),
        'name' => 'Pulsa Transfer',
        'display' => 'true',
    ],[
        'action' => 'order/pulsa-internasional',
        'image' => assets('images/menu/031-currency.svg'),
        'name' => 'Pulsa Internasional',
        'display' => 'true',
    ],[
        'action' => 'order/paket-internet',
        'image' => assets('images/menu/014-5g.svg'),
        'name' => 'Paket Internet',
        'display' => 'true',
    ],[
        'action' => 'order/paket-telepon',
        'image' => assets('images/menu/016-chatting.svg'),
        'name' => 'Paket SMS / Telpon',
        'display' => 'true',
    ],[
        'action' => 'order/token-pln',
        'image' => assets('images/menu/015-wireless.svg'),
        'name' => 'Token PLN',
        'display' => 'true',
    ],[
        'action' => 'order/voucher-game',
        'image' => assets('images/menu/003-game console.svg'),
        'name' => 'Voucher Game',
        'display' => 'true',
    ],[
        'action' => 'order/saldo-emoney',
        'image' => assets('images/menu/013-tap.svg'),
        'name' => 'Saldo E-Money',
        'display' => 'true',
    ],[
        'action' => 'order/streaming-tv',
        'image' => assets('images/menu/022-satellite.svg'),
        'name' => 'Streaming & TV',
        'display' => conf('xtra-fitur',1),
    ],[
        'action' => 'order/paket-lainnya',
        'image' => assets('images/menu/008-voucher.svg'),
        'name' => 'Voucher',
        'display' => 'true',
    ],[
        'action' => 'order/pascabayar',
        'image' => assets('images/menu/hot-sale.svg'),
        'name' => 'Pascabayar',
        'display' => conf('xtra-fitur',1),
    ],[
        'action' => 'order/bank-transfer',
        'image' => assets('images/menu/005-buy.svg'),
        'name' => 'Transfer Bank',
        'display' => 'true',
    ],[
    ],[
        'action' => 'order/akun-premium',
        'image' => assets('images/menu/004-smartphone.svg'),
        'name' => 'Akun Premium',
        'display' => 'true',
    ],[
        'action' => 'order/masa-aktif',
        'image' => assets('images/menu/004-smartphone.svg'),
        'name' => 'Masa Aktif',
        'display' => 'true',
    ],[
        'action' => 'order/game',
        'image' => assets('images/cards/gamepad.svg'),
        'name' => 'Game Feature',
        'display' => conf('xtra-fitur',2),
    ],[
        'action' => 'order/social-media',
        'image' => assets('images/menu/lastfmw.svg'),
        'name' => 'Social Media',
        'display' => conf('xtra-fitur',3),
    ],
]) ?>
        </div>
    </div>
    <div class="col-md-6 col-12">
        <h6 class="subtitle">News Updates <a href="<?= base_url('page/information') ?>" class="float-right small">View All</a></h6>
        <div class="card shadow border-0 mb-3" style="max-height: 400px; overflow: auto;">
            <div class="card-body">
<?php
$information = $call->query("SELECT * FROM information WHERE `type` != 'banner' ORDER BY id DESC LIMIT 4");
if($information->num_rows == 0) {
    print '<div class="row align-items-center"><div class="col"><h6 class="font-weight-small"><u style="font-weight:800" class="text-danger">SYSTEM</u></h6><div class="col-auto pl-0"><p class="small text-mute text-trucated mb-1">'.format_date('id',"$date $time").'</p></div><p class="small">No Information Available</p><hr></div></div>';
} else {
    while($row = $information->fetch_assoc()) {
        $text_color = 'success';
        if($row['type'] == 'info') $text_color = 'info';
        if($row['type'] == 'update') $text_color = 'primary';
        if($row['type'] == 'maintenance') $text_color = 'danger';
?>
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="font-weight-small"><p style="font-weight:800" class="badge badge-<?= $text_color ?>"><?= strtoupper($row['type']) ?></p></h6>
                        <div class="col-auto pl-0">
                            <p class="small text-mute text-trucated mb-1"><?= format_date('id',$row['date']) ?></p>
                        </div>
                        <p class="small"><?= base64_decode($row['content']) ?></p>
                        <hr>
                    </div>
                </div>
<?php } } ?>
            </div>
        </div>
    </div>
</div>
<?php require _DIR_('library/layout/footer.user'); } ?>