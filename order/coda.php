<?php 
require '../connect.php';
require _DIR_('library/session/user');

function gameMenu($x) {
    global $call;
    $out = '';
    for($i = 0; $i <= count($x)-1; $i++) {
        $cat = $x[$i];        
        $see = $call->query("SELECT * FROM con_game WHERE code = '$cat'")->fetch_assoc();    
        if($cat == $see['code']) $img = ($see['img'] !== '') ? base64_decode($see['img']) : 'https://i.pinimg.com/originals/5e/22/86/5e2286e02a8d3a65558ad3adf7534670.jpg';
        $out .= '<a href="javascript:;" onclick="javascript:location.href=\''.base_url('order/game/'.str_replace(' ','-',strtolower($cat))).'\'" class="category__product-container js-link-click"><img data-src="'.$img.'" alt="" class="category__product-image lozad img-thumbnail" src="'.$img.'" data-loaded="true"><div class="category__product-title">'.$cat.'</div></a>';        
    }
    return $out;
}

$search_banner = $call->query("SELECT * FROM information WHERE `type` = 'banner-game'");
    if($search_banner->num_rows == 0) {
        $banner[] = assets('banner/LannDigitalBanner.jpg');
    } else {
        while($row_banner = $search_banner->fetch_assoc()) {
            $banner[] = $row_banner['content'];
        }
    }
if(conf('xtra-fitur',2) <> 'true') exit(redirect(0,base_url()));
require _DIR_('library/layout/header.user');
?>
<style type="text/css">
    .category-container{max-width:760px;margin:2px auto 0}.category__product-row{display:block;max-width:100%;padding:0 5px 15px}.category__title{font-size:20px;font-family:Lato-Bold,sans-serif;padding-left:10px;padding-bottom:10px;text-transform:uppercase;color:#636363}*{box-sizing:border-box}@media only screen and (min-width:768px){.category__product-container{width:20%;padding:12px}}@media only screen and (max-width:768px){.category__product-container{width:25%;padding:12px;width:33.333333%}}.category__product-container{display:inline-block;text-align:center;text-decoration:none;cursor:pointer;padding:5px;vertical-align:top}.category__product-image{display:block;border-radius:15px;margin:auto;max-width:100%}.category__product-title{color:#636363;font-size:13px;padding-top:7px;max-width:128px;margin:auto}
</style>
<!-- Start Nav -->
<div class="row">
    <div class="col-md-6 offset-md-3">
    <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
       <li class="nav-item">
          <a class="nav-link active" data-toggle="pill" href="#menu1">Games</a>
       </li>
       <li class="nav-item">
          <a class="nav-link" data-toggle="pill" href="#menu2">Streaming</a>
       </li>
       <li class="nav-item">
          <a class="nav-link" data-toggle="pill" href="#menu3">Lainnya</a>
       </li>
    </ul>
    <!-- Start TabCon -->
    <div class="tab-content">
        <div id="menu1" class="container tab-pane active"><br>  
            <center>
              <h5>Populer</h5>
              <span class="d-block mb-1" style="background:#466ACB;width: 55px;height: 5px;border-radius: 10px;"></span>
           </center>
            <div class="category-container">
                <div class="category__product-row">
                    <div class="category__title"></div>
                    <?php
                    $search = $call->query("SELECT * FROM con_game WHERE populer = '1' AND lainnya != '1' ORDER BY code ASC");
                    while($row = $search->fetch_assoc()) { 
                       $lann[] = $row['code'];                        
                    } if(isset($lann)) if(is_array($lann)) print gameMenu($lann);
                    ?>
                </div>
            </div>
            <center>
              <h5>Games</h5>
              <span class="d-block mb-1" style="background:#466ACB;width: 55px;height: 5px;border-radius: 10px;"></span>
           </center>
            <div class="category-container">
                <div class="category__product-row">
                    <div class="category__title"></div>
                    <?php
                    // $search = $call->query("SELECT * FROM category WHERE `order` = 'game' AND status = 'available' ORDER BY name ASC");
                    $search = $call->query("SELECT category.* FROM category JOIN srv_game ON srv_game.game = category.name WHERE category.`order` = 'game' AND srv_game.provider != 'X' AND srv_game.status = 'available' GROUP BY category.name ORDER BY category.name ASC");
                    while($row = $search->fetch_assoc()) { 
                       $see_game = $call->query("SELECT * FROM con_game WHERE code = '".$row['name']."'")->fetch_assoc();
                       if($see_game['populer'] !== '1' && $see_game['lainnya'] !== '1') {
                           $xa[] = $row['name'];
                       } 
                    } if(isset($xa)) if(is_array($xa)) print gameMenu($xa);           
                    ?>
               </div>
           </div>
       </div>
       <div id="menu2" class="container tab-pane"><br>
           <center>
              <h5>Streaming</h5>
              <span class="d-block mb-1" style="background:#466ACB;width: 55px;height: 5px;border-radius: 10px;"></span>
           </center>

           <div class="category-container">
               <div class="category__product-row">
                   <div class="category__title"></div>
                   <?php
                   $search = $call->query("SELECT * FROM con_game WHERE lainnya = '1' AND populer != '1' ORDER BY code ASC");
                   while($row = $search->fetch_assoc()) { $lann_two[] = $row['code']; }
                   if(isset($lann_two)) if(is_array($lann_two)) print gameMenu($lann_two);
                   ?>
               </div>
           </div>
       </div>
       <div id="menu3" class="container tab-pane"><br>
           <center>
              <h5>Lainnya</h5>
              <span class="d-block mb-1" style="background:#466ACB;width: 55px;height: 5px;border-radius: 10px;"></span>
           </center>
           <div class="category-container">
               <div class="category__product-row">
                   <div class="category__title"></div>
                   <?php
                   $search = $call->query("SELECT category.* FROM category JOIN srv_game ON srv_game.game = category.name WHERE category.`order` = 'game' AND srv_game.provider = 'X' AND srv_game.status = 'available' GROUP BY category.name ORDER BY category.name ASC");
                    while($row = $search->fetch_assoc()) { 
                       $see_game = $call->query("SELECT * FROM con_game WHERE code = '".$row['name']."'")->fetch_assoc();
                       if($see_game['populer'] !== '1' && $see_game['lainnya'] !== '1') {
                           $lann_three[] = $row['name'];
                       } 
                    } if(isset($lann_three)) if(is_array($lann_three)) print gameMenu($lann_three);
                   ?>                 
              </div>
          </div>
      </div>
  </div></div></div>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/lozad/dist/lozad.min.js"></script>
<?php require _DIR_('library/layout/footer.user'); ?>