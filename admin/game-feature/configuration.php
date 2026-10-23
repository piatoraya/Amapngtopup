<?php 
require '../../connect.php';
require _DIR_('library/session/admin');
del_con_game(); addCog();

if(isset($_POST['add_image'])) {
    $web_code = filter($_POST['web_code']);    
    $post_content = $_FILES['content'];
    $img_check = shenn_image_check($post_content, 3000000, ['image/jpeg','image/png','image/webp']);
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
    } else if($img_check['result'] == false) {
        ShennXit(['type' => false,'message' => $img_check['message']]);
    } else {
        $newFile = date('YmdHis').'.'.$img_check['ext'];
        if(move_uploaded_file($post_content['tmp_name'], _DIR_('library/assets/lann/'.$newFile,''))) {
            $lann_img = assets('lann/'.$newFile);
            $call->query("UPDATE con_game SET img = '".base64_encode($lann_img)."' WHERE code = '$web_code'");
            ShennXit(['type' => true,'message' => 'Image added successfully.']);
        } else {
            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
        }
    }
} if(isset($_POST['edit_image'])) {
    $web_code = filter($_POST['web_code']);
    $post_content = $_FILES['content'];
    $img_check = shenn_image_check($post_content, 3000000, ['image/jpeg','image/png','image/webp']);
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
    } else if($img_check['result'] == false) {
        ShennXit(['type' => false,'message' => $img_check['message']]);
    } else if($call->query("SELECT * FROM con_game WHERE code = '$web_code'")->num_rows == 0) {
        ShennXit(['type' => false,'message' => 'Data not found.']);
    } else {
        $oldData = $call->query("SELECT * FROM con_game WHERE code = '$web_code'")->fetch_assoc();
        $newFile = date('YmdHis').'.'.$img_check['ext'];
        $oldPic = _DIR_(str_replace(base_url(),'',base64_decode($oldData['img'])),'');
        
        if(file_exists($oldPic)) {
            if(unlink($oldPic)) {
                if(move_uploaded_file($post_content['tmp_name'], _DIR_('library/assets/lann/'.$newFile,''))) {
                    $call->query("UPDATE con_game SET img = '".base64_encode(base_url('library/assets/lann/'.$newFile))."' WHERE code = '$web_code'");
                    ShennXit(['type' => true,'message' => 'Image updated successfully.'],base_url('admin/game-feature/configuration-game'));
                } else {
                    ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
                }
            } else {
                ShennXit(['type' => false,'message' => 'Failed to delete old Image.']);
            }
        } else {
            if(move_uploaded_file($post_content['tmp_name'], _DIR_('library/assets/lann/'.$newFile,''))) {
                $call->query("UPDATE con_game SET img = '".base64_encode(base_url('library/assets/lann/'.$newFile))."' WHERE code = '$web_code'");
                ShennXit(['type' => true,'message' => 'Image updated successfully.'],base_url('admin/game-feature/configuration-game'));
            } else {
                ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
            }
        }
    }
}

if(isset($_POST['ce'])) {
    $web_code = filter($_POST['web_code']);
    $post_populer = isset($_POST['populer']) ? filter_entities($_POST['populer']) : false;
    $post_lainnya = isset($_POST['lainnya']) ? filter_entities($_POST['lainnya']) : false;
    $post_inf = isset($_POST['inf']) ? filter(base64_encode($_POST['inf'])) : '';
    
    $data_cfg = $call->query("SELECT * FROM con_game WHERE code = '$web_code'")->fetch_assoc();
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);    
    } else {              
        $pst_pop = ($post_populer == 'true') ? '1' : '0';
        $pst_lain = ($post_lainnya == 'true') ? '1' : '0';
        
        if($pst_pop == '1' && $pst_lain == '1') {
            ShennXit(['type' => false,'message' => 'Error system, cannot be compared to popular and others.']);
        } else {
            $upd = $call->query("UPDATE con_game SET info = '$post_inf', populer = '$pst_pop', lainnya = '$pst_lain' WHERE code = '$web_code'");                    
            if($upd == true) {
                ShennXit(['type' => true,'message' => ucfirst($web_code).' Successfully updated.']);                       
            } else {
                ShennXit(['type' => false,'message' => 'Error update [].']);
            }
        }        
    }
}

if(isset($_POST['form_edit'])) {
    $web_code = filter($_POST['web_code']);
    $post_form = filter($_POST['inform']);
    $post_place1 = filter($_POST['place_one']);
    $post_place2 = filter($_POST['place_two']);
         
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);    
    } else {
        $json_place = json_encode(['place_one' => $post_place1,'place_two' => $post_place2]);              
        $post_cgame = filter($_POST['cid']);
        $post_valid = (!$post_cgame) ? '0' : '1';
        
        $upd = $call->query("UPDATE con_game SET form = '$post_form', place = '$json_place', validate = '$post_valid', code_id = '$post_cgame' WHERE code = '$web_code'");
        if($upd == true) {
            ShennXit(['type' => true,'message' => ucfirst($web_code).' Successfully updated.']);
        } else {
            ShennXit(['type' => false,'message' => 'Data update Failed.']);
        }           
    }
}

/*
if(isset($_POST['add_server'])) {
    $web_code = filter($_POST['web_code']);
    $pst_v1 = filter($_POST['v1']);
    $pst_n1 = filter($_POST['n1']);
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);    
    } else if(!$pst_v1 || !$pst_n1) {
        ShennXit(['type' => false,'message' => 'Value and Name, still blank Form.']);
    } else if(!$web_code) {
        ShennXit(['type' => false,'message' => 'Web Code not detected.']);
    } else {
        if($call->query("INSERT INTO con_game_server VALUES (null,'$web_code','$pst_v1','$pst_n1')") == true) {
            $call->query("UPDATE con_game SET server = '1' WHERE code = '$web_code'");
            ShennXit(['type' => true,'message' => ucfirst($web_code).' Successfully updated.']);
        } else {
            ShennXit(['type' => false,'message' => 'Trauble.']);
        }
    }   
}
*/

require _DIR_('library/layout/header.admin');
?><section id="faq-search">
    <div class="row">
        <div class="col-12">
            <div class="card faq-bg white">
                <div class="card-content">
                    <div class="card-body p-sm-4 p-2">
                        <h1 class="white">Search?</h1>
                        <p class="card-text mb-2">Cari nama yang ingin Anda cari.</p>
                        <form method="POST">
                            <fieldset class="form-group position-relative has-icon-left mb-0">
                                <input type="text" class="form-control form-control-lg" id="searchbar" placeholder="Search">
                                <div class="form-control-position">
                                    <i class="feather icon-search px-1"></i>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="faq">
    <div class="row">
        <div class="col-xl-12 col-md-12 col-12">
            <div class="card bg-transparent border-0 shadow-none collapse-icon accordion-icon-rotate">
                <div class="card-body p-0">
                    <div class="accordion search-content-info" id="accordionExample">
<?php
$search = $call->query("SELECT * FROM con_game ORDER BY code ASC");
while($row = $search->fetch_assoc()) {
$place = json_decode($row['place'], true);
$add_image_game = base_url('admin/game-feature/modal/add-image?s='.$row['code']);
$add_form_game = base_url('admin/game-feature/modal/edit-form?s='.$row['code']);
$edit_image_game = base_url('admin/game-feature/modal/edit-image?s='.$row['code']);
$add_server_game = base_url('admin/game-feature/modal/add-server?s='.$row['code']);
$see_srv = $call->query("SELECT * FROM srv_game WHERE game = '".$row['code']."'")->fetch_assoc();
?>
                        <div class="collapse-margin search-content">
                            <div class="card-header" id="heading<?= $row['id'] ?>" role="button" data-toggle="collapse" data-target="#collapse<?= $row['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $row['id'] ?>">
                                <span class="lead collapse-title"><?= $row['code'] ?></span>
                            </div>
                            <div id="collapse<?= $row['id'] ?>" class="collapse" aria-labelledby="heading<?= $row['id'] ?>" data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                           <form method="POST">
                                                <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $csrf_string ?>">
                                                <input type="hidden" name="web_code" value="<?= $row['code'] ?>">
                                                                                                                                                                                                                                                                                                
                                                <div class="form-label-group">
                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                        <input type="checkbox" name="populer" value="true" <?= ($row['populer'] == '1') ? 'checked' : '' ?>>
                                                        <span class="vs-checkbox vs-checkbox-sm">
                                                             <span class="vs-checkbox--check">
                                                                  <i class="vs-icon feather icon-check"></i>
                                                              </span>
                                                        </span>
                                                        <span>Tampilkan Populer?</span>
                                                    </div>
                                                </div>           
                                                <div class="form-label-group">
                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                        <input type="checkbox" name="lainnya" value="true" <?= ($row['lainnya'] == '1') ? 'checked' : '' ?>>
                                                        <span class="vs-checkbox vs-checkbox-sm">
                                                             <span class="vs-checkbox--check">
                                                                  <i class="vs-icon feather icon-check"></i>
                                                              </span>
                                                        </span>
                                                        <span>Tampilan Streaming?</span>
                                                    </div>
                                                </div>                                                                                            
                                                <hr>
                                                <div class="form-group">
                                                    <a href="javascript:;" <?= onclick_modal('Add Image',$add_image_game, 'lg') ?>><i class="feather font-medium-3 icon-plus-circle text-secondary"></i></a> Add Image
                                                </div>
                                                <div class="form-group">
                                                    <a href="javascript:;" <?= onclick_modal('Edit Image',$edit_image_game, 'lg') ?>><i class="feather font-medium-3 icon-edit text-secondary"></i></a> Edit Image
                                                </div>
                                                <div class="form-group">
                                                    <a href="javascript:;" <?= onclick_modal('Edit Form',$add_form_game, 'lg') ?>><i class="feather font-medium-3 icon-plus-circle text-secondary"></i></a> Edit Form
                                                </div>
                                                <div class="form-group">
                                                    <a href="javascript:;" <?= onclick_modal('Add Server',$add_server_game, 'lg') ?>><i class="feather font-medium-3 icon-plus-circle text-secondary"></i></a> Add Server
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <label>Content</label>
                                                    <textarea name="inf" class="form-control" row="5"><?= base64_decode($row['info']) ?></textarea><br><small class="text-danger">Pisahkan informasi dengan tanda | Contoh: Server 24Jam|Proses 1×24Jam. sampai Paragraf 5</small>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-6">
                                                        <button type="button" class="btn btn-danger btn-block"> RESET </button>
                                                    </div>
                                                    <div class="form-group col-6">
                                                        <button type="submit" name="ce" class="btn btn-primary btn-block"> SUBMIT </button>
                                                    </div>
                                                </div>
                                           </form>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
<?php } ?>
                   </div>
               </div>
           </div>
       </div>
   </div>
</section>
<script type="text/javascript">
function LannAct(form, link) {
	$.ajax({
	    url: link,
		type: 'POST',
		dataType: 'html',
		data: $(form).serialize(),
		success: function(data) {
		    $('#sess-result-modal').html(data);
			$('#sess-result').html(data);
			$('#datatable').DataTable().ajax.reload();
		}, error: function() {
		    $('#sess-result').html(error_result);
		}, beforeSend: function() {
		    $('#sess-result-modal').html();
		}
	});
}
</script>
<?php require _DIR_('library/layout/modal'); require _DIR_('library/layout/footer.user'); ?>
