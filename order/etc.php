<?php 
require '../connect.php';
require _DIR_('library/session/user');

$get_s = isset($_GET['s']) ? strtolower(filter($_GET['s'])) : '';
if($get_s == 'pulsa-internasional') $title = 'Pulsa Internasional';
else if($get_s == 'token-pln') $title = 'Token Listrik (PLN)';
else if($get_s == 'saldo-emoney') $title = 'Saldo E-Money';
else if($get_s == 'voucher-game') $title = 'Voucher Game';
else if($get_s == 'streaming-tv') $title = 'Streaming & TV';
else if($get_s == 'akun-premium') $title = 'Akun Premium';
else if($get_s == 'paket-lainnya') $title = 'Paket & Kategori Lainnya';
else if($get_s == 'akun-premium') $title = 'Akun Premium';
else if($get_s == 'masa-aktif') $title = 'Masa Aktif';
else exit(redirect(0,base_url()));

require _DIR_('library/shennboku.app/order-prepaid');
require _DIR_('library/layout/header.user');
?>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="row">
    <div class="col-12 col-md-10 offset-md-1">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title"><?= $title ?></h4>
                <hr>
                <form method="POST">
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" name="category" id="category">
                            <option value="0">- Choose One -</option>
                            <?php
                            $search = $call->query("SELECT * FROM category WHERE type = '$get_s' AND `order` = 'prepaid' ORDER BY name ASC");
                            while($row = $search->fetch_assoc()) {
                                if($call->query("SELECT * FROM srv_ppob WHERE brand = '".$row['code']."' AND type = '$get_s' AND status = 'available'")->num_rows > 0)
                                    print '<option value="'.$row['code'].'">'.$row['name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Target</label>
                        <input type="text" class="form-control" id="data" name="data">
                    </div>
                    <?php if($get_s == 'token-pln') { ?>
                    <div class="form-group d-none" id="lock-check">
                        <label class="text-danger">* Click info name PLN</label>
                        <input type="text" class="form-control" id="out" readonly>
                    </div>
                    <?php } ?>
                    <label>Select Service</label>
                    <div class="row" id="service">
                        <div class="col-12">
                            <div class="alert alert-danger bg-danger text-white border-0" role="alert">Please select a category.</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require _DIR_('library/layout/modal'); require _DIR_('library/layout/footer.user'); ?>
<script type="text/javascript">
$(document).ready(function() {
    $("#data").keyup(function() {
        var category = $("#category").val(), target = $("#data").val();
        ShennAJAX(category,target);   
        
        if(target > 12345678910) {
           $("#lock-check").removeClass('d-none');
        } else {
            reset();
        }
       
    });
    $("#category").change(function() {
        var category = $("#category").val(), target = $("#data").val();
        if(target != '') ShennAJAX(category,target);
    });
    function ShennAJAX(category,target) {
        $.ajax({
            type: 'POST',
            data: 'category=' + category + '&shenn=' + target + '&type=<?= $get_s ?>&csrf_token=<?= $csrf_string ?>',
            url: '<?= ajaxlib('service-prepaid') ?>',
            dataType: 'html',
            success: function(msg) {
                $("#service").html(msg);
            }
        });
    }
    
    $("#out").click(function() {
        var output = $("#data").val();    
       
        $.ajax({
	    url: '<?= ajaxlib('pln-check') ?>',
	    data: 'meter='+output+'&csrf_token=<?= $csrf_string ?>',
	    type: 'POST',
	    dataType: 'html',
	    beforeSend: function() {
                Swal.fire({title:"Harap Tunggu...",showConfirmButton:false,allowOutsideClick:false});
            },
	    success: function(res) {
	        $("#out").val(res);
	        if(res != 'Data tidak ditemukan') {
	            Swal.fire(
	               'Success!',
	               'Data ditemukan!',
	               'success'
	            );
	        } else {
	            Swal.fire(
	               'Failed!',
	               res,
	               'error'
	            );
	        }
	    }
        });
    });   
});
function prepaid(endpoint) {
    var target = $("#data").val(); 
    modal('Order Confirmation', endpoint + target, 'lg');    
}

function reset() {
    $("#lock-check").addClass('d-none');
}

</script>