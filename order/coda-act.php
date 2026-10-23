<?php 
require '../connect.php';
require _DIR_('library/session/user');

$get_s = isset($_GET['s']) ? str_replace('-',' ',filter($_GET['s'])) : '';
$search_cat = $call->query("SELECT * FROM category WHERE name = '$get_s' AND `order` = 'game'");
if($search_cat->num_rows == 0) exit(redirect(0,base_url('order/game')));
$data_cat = $search_cat->fetch_assoc();
$operator = $data_cat['name'];

if(conf('xtra-fitur',2) <> 'true') exit(redirect(0,base_url()));
require _DIR_('library/shennboku.app/order-games');
require _DIR_('library/layout/header.user');

$see = $call->query("SELECT * FROM con_game WHERE code = '$operator'");
while($lann_see = $see->fetch_assoc()) {
   // LIST USERID
   if($lann_see['form'] == '1') {
       $lann_game_list[] = $lann_see['code'];
   } else if($lann_see['form'] == '2') {
       $lann_game_list[] = $lann_see['code'];
   } else {
       $lann_game_list[] = '';
   }
   
   // VALIDATE ON OFF
   if($lann_see['validate'] == '1') {
       $lann_game_valid[] = $lann_see['code'];
   } else {
       $lann_game_valid[] = '';
   }
   
   if($lann_see['code'] == $operator) {
       $holder = json_decode($lann_see['place'], true);   
       if($lann_see['form'] == '1') {       
           $input_manajer = '<div class="form-group col-md-4"><input type="text" class="form-control" name="target" id="target" placeholder="'.$holder['place_one'].'" required></div>';
       } else if($lann_see['form'] == '2') {
           $input_manajer = '<div class="form-group col-md-4"><input type="text" class="form-control" name="target" id="target" placeholder="'.$holder['place_one'].'" required></div><div class="form-group col-md-4"><input type="text" class="form-control" name="target2" id="target2" placeholder="'.$holder['place_two'].'" required></div>';
       } else {
           $input_manajer = '';
       }
       
       if($lann_see['server'] == '1') {
           $s = $call->query("SELECT * FROM con_game_server WHERE code = '$operator'");
           if($s->num_rows > 0) {
               $opt = '<option value="" selected disabled>- Select Server -</option>';           
               while($r = $s->fetch_assoc()) {
                   $opt .= '<option value="'.$r['value'].'">'.$r['name'].'</option>';
               } $input_server = '<div class="form-group col-md-4"><select class="form-control" name="target2">'.$opt.'</select></div>';              
           } else {
               $call->query("UPDATE con_game SET server = '0' WHERE code = '$operator'");
               $input_server = '';
           }
       }
   }
} $list_nickname = $lann_game_valid;
  $list_userid = $lann_game_list;

function iGame($x) {
    global $call;    
    $see = $call->query("SELECT * FROM con_game WHERE code = '$x' AND info != ''");  
    if($see->num_rows == 0) {
        return '';
    } else {          
        $row = $see->fetch_assoc();
        $txt = base64_decode($row['info']);
        $content = explode('|',$txt); 
        $one_con = ($content[0] != '') ? '<i class="fas fa-angle-double-right"></i> '.$content[0].'<br>' : '';
        $two_con = ($content[1] != '') ? '<i class="fas fa-angle-double-right"></i> '.$content[1].'<br>' : '';
        $three_con = ($content[2] != '') ? '<i class="fas fa-angle-double-right"></i> '.$content[2].'<br>' : '';
        $four_con = ($content[3] != '') ? '<i class="fas fa-angle-double-right"></i> '.$content[3].'<br>' : '';
        $five_con = ($content[4] != '') ? '<i class="fas fa-angle-double-right"></i> '.$content[4].'<br>' : '';        
        if($x == $row['code']) {
            return '<div class="row">
            <div class="col-12 col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-body">
                        <h6 class="font-weight-small"><b style="font-weight:800" class="badge badge-default"><i class="fa fa-bullhorn"></i> INFO</b></h6>
                        '.$one_con.''.$two_con.''.$three_con.''.$four_con.''.$five_con.'                                            
                    </div>
                </div>
            </div>
        </div>';
        } else {
            return '';
        }
    }
}
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.7.95/css/materialdesignicons.css" rel="stylesheet" type="text/css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<style>
input.checked[type="radio"]{
    visibility:visible;
    margin-left:-5px;
    margin-right:2px;
}
    
input[type='radio']:checked:after {
    width: 15px;
    height: 15px;
    border-radius: 15px;
    background-color: #02c916;
    content: '';
    display: inline-block;
    visibility: visible;
    border: 2px solid white;
}
</style>
<?= iGame($operator) ?>
<form method="POST" id="myForm">
    <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
    <div class="row">
        <div class="col-12 col-md-10 offset-md-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">
                        <i class="mdi mdi-numeric-1-circle-outline text-primary"></i>
                        <b>Masukan Data Tujuan</b>
                    </h4>
                    <div class="row">
                        <?php if(in_array($operator, $list_userid)) { ?>
                        <?= $input_manajer ?>
                        <?= $input_server ?>
                        <?php } if(in_array($operator, $list_nickname)) { ?>
                        <div class="form-group col-md-4">
                            <input type="text" class="form-control" name="displayname" id="displayname" placeholder="NickName" readonly>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-10 offset-md-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">
                        <i class="mdi mdi-numeric-2-circle-outline text-primary"></i>
                        <b>Pilih Server</b>
                    </h4>
                    <div class="row">
                        <?php
                        $search = $call->query("SELECT * FROM srv_game WHERE game = '$operator' AND status = 'available' ORDER BY server ASC"); $l=[];
                        while($row = $search->fetch_assoc()) {
                            if(!in_array($row['server'],$l)){
                                $l[]=$row['server'];
                        ?>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                            <label class="btn btn-outline-dark col-md-auto w-100 mb-1 rounded waves-effect waves-light text-white" style="white-space:normal !important;text-align:center !important;border-color:#007BFF !important;background-color:#007BFF;font-weight: 500;letter-spacing: 0.5px;line-height: 1.2;">
                                <input class="checked" type="radio" name="server" id="server" value="<?= $row['server'] ?>">
                                <?= $row['server'] ?>
                            </label>
                        </div>
                        <?php } } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-10 offset-md-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">
                        <i class="mdi mdi-numeric-3-circle-outline text-primary"></i>
                        <b>Pilih Nominal Topup</b>
                    </h4>
                    <div class="row" id="service"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-10 offset-md-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">
                        <i class="mdi mdi-numeric-4-circle-outline text-primary"></i>
                        <b>Harga</b>
                    </h4>
                    <div class="row">
                        <input type="hidden" name="jumlah" class="form-control" id="jumlah" value="1" readonly>
                        <div class="form-group col-12">
                            <input type="text" class="form-control" name="harga" id="harga" placeholder="Rp 0" readonly>
                            <small class="text-danger">*Pastikan ID yang anda masukan benar! Kesalahan input bukan tanggung jawab kami.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-10 offset-md-1">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">
                        <i class="mdi mdi-numeric-5-circle-outline text-primary"></i>
                        <b>Keamanan</b>
                    </h4>
                    <div class="row">
                        <div class="form-group col-12 col-md-8">
                            <div class="input-group">
                                <input type="password" inputmode="numeric" class="form-control" id="password" name="pin" data-toggle="password" onkeyup="this.value=this.value.replace(/[^\d]+/g,'')" maxlength="6" minlength="6" placeholder="Two-Factor Authorization (PIN/2FA)" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-eye"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <button type="submit" id="actionbutton" name="order" class="btn btn-primary btn-block">Order</button>
                            <div id="timerdiv" style="display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php require _DIR_('library/layout/footer.user'); ?>
<script type="text/javascript">
$(document).ready(function() {
    $("#actionbutton").click(function() {
        $("#actionbutton").hide();
        $("#timerdiv").show(); 
        var timer = 10;     
        function counting(){
            var rto = setTimeout(counting,1000);
            $('#timerdiv').html( '<button type="button" class="btn btn-primary btn-block btn-disabled"> Processing </button>' );
            timer--;
            if(timer < 0) {
                clearTimeout(rto);
                timer = 10;
                $("#timerdiv").hide();             
                $("#actionbutton").show();
            }
        }
        counting();
     });
     
    $('input[name="target"],input[name="target2"],select[name="target2"]').change(function(){
        var safalian = <?= json_encode($list_nickname) ?>;
        var shennZone = <?= json_encode($list_nickname) ?>;
        var target = $("#target").val();
        var target2 = $("#target2").val();
        var operator = '<?= $operator ?>';
        
        if(safalian.includes(operator) == true) {
            if(shennZone.includes(operator) == true && target != '' && target2 != '') {
                var postdata = 'category=' + operator + '&target=' + target + '&target2=' + target2;
                validasi(postdata);
            } else if(shennZone.includes(operator) == false && target != '') {
                var postdata = 'category=' + operator + '&target=' + target;
                validasi(postdata);
            }
        }
    });
    
    function validasi(postdata){
        $.ajax({
            url: '<?= base_url('order/ajax/check') ?>',
            data: postdata,
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {
                Swal.fire({title:"Harap Tunggu...",showConfirmButton:false,allowOutsideClick:false});
            },
            success: function(shenn) {
                if(shenn.result == false){
                    Swal.fire('Ups...',' ' + shenn.message,'error');
                    document.getElementById("myForm").reset();
                }else{
                    Swal.fire('Sukses !',"Player Id ditemukan",'success');
                    document.getElementById("displayname").value = decodeURI(shenn.data);
                }
    	    }
    	});
    };
	
    $('input[name="server"]').change(function() {
        var server = $('input[name=server]:checked', '#myForm').val();
        var target = $("#target").val();
        
        if(!target) {
            Swal.fire('Ups...','Masukan data tujuan','error');
            document.getElementById("myForm").reset();
            server = 0;            
        }
        
        $.ajax({
            url: '<?=base_url('order/ajax/service')?>',
            data: 'category=<?= $operator ?>&server=' + server,
            type: 'POST',
            dataType: 'html',
            beforeSend: function() {
                $("#service").html("<div class=\"col-12\">Please wait..</div>");
            },
            success: function(msg) {
                $("#service").html(msg);             
                reset();
            }
        });
    });

    $('input[name="jumlah"]').keyup(function() {
        var server =$('input[name=server]:checked', '#myForm').val();
        var jumlah = $("#jumlah").val();
        var layanan =  $('input[name=layanan]:checked', '#myForm').val();

        if(!server){
            Swal.fire('Ups...','Pilih Server Dulu','error');
            reset();
        }
        
        $.ajax({
            url: '<?=base_url('order/ajax/price')?>',
            data: 'server=' + server + '&jumlah=' + jumlah + '&layanan=' + layanan + '&category=<?= $operator ?>',
            type: 'POST',
            dataType: 'html',
            beforeSend: function() {
                $("#harga").val("Please wait..");
            },
            success: function(msg) {
                $("#harga").val(msg);
            }
    	});
    });
});

function reset() {
    document.getElementById("jumlah").value = '';
    document.getElementById("harga").value = '';
}
</script>