<?php
if(!isset($call)) die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");
$search1 = $call->query("SELECT * FROM deposit WHERE user = '$sess_username' AND status = 'unpaid'");

function get_fee_atlantic($wid,$tid,$code) {
    global $curl;
    $data_prov = squery("SELECT * FROM provider WHERE code = '$code'")->fetch_assoc();
    
    $connect = $curl->connectPost($data_prov['link'].'deposit/status',['api_key' => $data_prov['apikey'],'id' => $tid]);
    if($connect['status'] == true) {
        $lann_fee = $connect['data']['tambahan'];
        squery("UPDATE deposit SET uniq = '$lann_fee' WHERE wid = '$wid'");
    }
}

if(isset($_POST['confirm'])) {
    $post_rid = date('YmdHis').random_number(2);
    $post_payment = filter($_POST['payment']);
    $post_method = filter_entities($_POST['method']);
    $post_sender = ($_POST['phone'] !== '') ? filter_phone('62',$_POST['phone']) : '';
    $post_quantity = filter_entities($_POST['quantity']);
    $post_kodeuniq = ($post_payment == 'pulsa') ? '0' : rand(001,410);
       
    $post_dataup = conf('deporeq',1).' '.conf('deporeq',2);    
    $post_expired = date('Y-m-d H:i:s',strtotime('+'.$post_dataup, strtotime($dtme)));
    
    $data_depo = $call->query("SELECT * FROM deposit_method WHERE id = '$post_method'")->fetch_assoc();
	$post_note = $data_depo['data'];
	$post_name = $data_depo['name'];
	$post_vendr = $data_depo['method'];
	
	$post_price3 = ($data_depo['xfee'] == '-') ? $data_depo['fee'] : ($post_quantity * $data_depo['fee']);
    $post_price2 = $post_quantity - $post_price3;
    $post_price1 = $post_price2 * $data_depo['rate'];
    $post_price = $post_price2 - $post_price1;
	
	$check_operators = $SimCard->operator($post_sender);
    $check_operator = (in_array($check_operators, ['Axis','XL'])) ? 'axiata' : strtolower($check_operators);
    if($post_payment == 'pulsa') {
        $gte_max = "999999";
        $gte_acc = ($post_vendr === $check_operator) ? true : false;
	} else {
        $gte_max = "9999999";
        $gte_acc = true;
	}
	$gte_sal = $post_price1;
	$gte_fee = $post_price3+$post_price;
	$gte_min = $data_depo['min'];
     
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if($_CONFIG['mt']['topup'] == 'true') {
        ShennXit(['type' => false,'message' => 'There is a deposit system maintenance, please try again later.']);
    } else if(!$post_method || !$post_quantity) {
        ShennXit(['type' => false,'message' => 'There is still a blank form.']);
    } else if($post_payment == 'pulsa' && $gte_acc == false) {
        ShennXit(['type' => false,'message' => ucfirst($check_operators).' numbers are not supported in this method.']);
    } else if($post_quantity < $gte_min) {
        ShennXit(['type' => false,'message' => 'The minimum deposit amount is IDR '.number_format($gte_min).'.']);
    } else if($post_quantity > $gte_max) {
        ShennXit(['type' => false,'message' => 'The maximum deposit amount is IDR '.number_format($gte_max).'.']);
    } else if($call->query("SELECT id FROM deposit WHERE user = '$sess_username' AND status = 'unpaid'")->num_rows >= 3) {
        ShennXit(['type' => false,'message' => 'You still have unpaid deposit requests.']);
    } else if($call->query("SELECT wid FROM deposit WHERE wid = '$post_rid'")->num_rows > 0) {
        ShennXit(['type' => false,'message' => 'Clashing System, please try again.']);
    } else {      
        $method_auto = $data_depo['auto'];
        if($method_auto == 1) {
            $method_before = ['bank','emoney','virtual'];
            $method_after = ['bank','ewallet','va'];
            $method_replace = str_replace($method_before,$method_after,$post_payment);
            $search_prov = $call->query("SELECT * FROM provider WHERE code = 'ATL'");
            if($search_prov->num_rows == 1) {
                $data_prov = $search_prov->fetch_assoc();
                
                /* Start Curl */
                $params = [
                    'api_key' => $data_prov['apikey'],
                    'reff_id' => $post_rid,
                    'nominal' => $post_quantity,
                    'type' => $method_replace,
                    'metode' => $post_vendr
                ]; $try = $curl->connectPost($data_prov['link'].'deposit/create',$params);
                file_put_contents('deposit.json', json_encode($try, JSON_PRETTY_PRINT));
                if(isset($try['status'])) {
                    if($try['status'] == true) {
                        $lann_out = $try['data'];
                        $lann_tid = $lann_out['id'];
                        
                        /* QRIS ONLY */
                        $lann_qris = $lann_out['qr_image'];
                        
                        /* URL ONLY */
                        $lann_url = $lann_out['url'];
                        
                        /* TUJUAN AN ONLY */
                        $lann_number = $lann_out['tujuan'];
                        $lann_atnama = $lann_out['atas_nama'];
                        
                        /* TUJUAN VIRTUAL */
                        $lann_virtual = $lann_out['nomor_va'];
                        
                        /* QRIS ONLY */
                        $lann_qris_string = (stristr(strtolower($data_depo['method']),'qris')) ? true : false;
                        
                        if($data_depo['type'] == 'bank') {
                            $lann_note = "$lann_number A/n $lann_atnama";
                        } else if($data_depo['type'] == 'emoney' && $lann_qris_string == false) {
                            $lann_note = $lann_url;
                        } else if($lann_qris_string == true && $data_depo['type'] == 'emoney') {
                            $lann_note = $lann_qris;
                        } else if($data_depo['type'] == 'virtual') {
                            $lann_note = "$lann_virtual A/n ";
                        }
                            
                        $ins = $call->query("INSERT INTO deposit VALUES (null,'$post_rid','$sess_username','$post_vendr','$post_name','$lann_note','$lann_tid','0','$gte_fee','$gte_sal','unpaid','$date $time','$post_expired')");
                        if($ins == true) {
                            get_fee_atlantic($post_rid,$lann_tid,'ATL');
                            redirect(0,base_url('deposit/invoice/'.$post_rid));
                        } else {
                            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
                        }
                    } else {
                        ShennXit(['type' => false,'message' => $try['message']]);
                    }
                } else {
                    ShennXit(['type' => false,'message' => 'Connection Failed!']);
                }                                           
            } else {
                ShennXit(['type' => false,'message' => 'Server not found!']);
            }
        } else {
            $ins = $call->query("INSERT INTO deposit VALUES (null,'$post_rid','$sess_username','$post_vendr','$post_name','$post_note','$post_sender','$post_kodeuniq','$gte_fee','$gte_sal','unpaid','$date $time','$post_expired')");
            if($ins == true) {
                redirect(0,base_url('deposit/invoice/'.$post_rid));
            } else {
                ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
            }
        }
    }
}
                              
                            
        /*
        if(!empty($method_auto)) {         
            $method_before = ['bank','emoney','virtual'];
            $method_after = ['bank','ewallet','va'];
            $method_replace = str_replace($method_before,$method_after,$post_payment);
            $data_prov = $call->query("SELECT * FROM provider WHERE code = 'ATL'")->fetch_assoc();
           
            $try = $curl->connectPost($data_prov['link'].'deposit/create',['api_key' => $data_prov['apikey'],'reff_id' => $post_rid,'nominal' => $post_quantity,'type' => $method_replace,'metode' => $method_auto]);
            
            if(isset($try['status'])) {
                if($try['status'] == true) {
                    $tid = $try['data']['id'];
                    $qr_image = $try['data']['qr_image'];
                    $url_i = $try['data']['url'];
                    $tujuan = $try['data']['tujuan'];
                    $atas_nama = $try['data']['atas_nama'];
                    $number_virtual = $try['data']['nomor_va'];
                    
                    if($data_depo['type'] == 'emoney' && $data_depo['method'] !== 'qris') {
                        $post_noted = base64_encode($url_i);
                    } else if($data_depo['type'] == 'bank' && $data_depo['method'] !== 'qris') {
                        $post_noted = $tujuan.' A/n '.$atas_nama;
                    } else if($data_depo['method'] == 'qris' && $data_depo['type'] == 'emoney') {
                        $post_noted = base64_encode($qr_image);
                    } else if($data_depo['type'] == 'virtual' && !in_array($data_depo['type'],['emoney','qris','bank'])) {
                        $post_noted = $number_virtual;
                        
                    }
                    
                    $ins = $call->query("INSERT INTO deposit VALUES (null,'$post_rid','$sess_username','$post_vendr','$post_name','$post_noted','".base64_encode($tid)."','0','$gte_fee','$gte_sal','unpaid','$date $time','$post_expired')");
                    if($ins == true) {
                        get_fee_atlantic($post_rid,$tid,'ATL');
                        redirect(0,base_url('deposit/invoice/'.$post_rid));
                    } else {
                        ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
                    }
                } else {
                    ShennXit(['type' => false,'message' => $try['message']]);
                }
            } else {
                ShennXit(['type' => false,'message' => 'Connection Failed!']);
            }
        } else {
            $in = $call->query("INSERT INTO deposit VALUES (null,'$post_rid','$sess_username','$post_vendr','$post_name','$post_note','$post_sender','$post_kodeuniq','$gte_fee','$gte_sal','unpaid','$date $time','$post_expired')");
            if($in == true) {
                redirect(0,base_url('deposit/invoice/'.$post_rid));
            } else {
                ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
            }
        }
    }
}*/     