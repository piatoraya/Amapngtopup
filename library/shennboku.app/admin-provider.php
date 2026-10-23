<?php
if(!isset($call)) die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");
function ShennCheck($code,$uid,$ukey) {
    global $curl; global $call;
    if($call->query("SELECT * FROM provider WHERE code = '$code'")->num_rows == 1) {
        $data = $call->query("SELECT * FROM provider WHERE code = '$code'")->fetch_assoc();
        if($code == 'FLZ') {
            $json = $curl->connectPost($data['link'].'/profile',['key' => $ukey]);
            if(isset($json['result'])) {
                if($json['result'] == true) {
                    $res = ['type' => true,'data' => $json['data']];
                    $call->query("UPDATE provider SET balance = '".$json['data']['balance']."' WHERE code = '$code'");
                }
                if($json['result'] == false) $res = ['type' => false,'msg' => $json['data']];
            } else {
                $res = ['type' => false,'msg' => 'Connection Failed!'];
            }
        } else if($code == 'VP') {
            $json = $curl->connectPost($data['link'].'profile',['key' => $ukey,'sign' => md5($uid.$ukey)]);
            if(isset($json['result'])) {
                if($json['result'] == true) {
                    $res = ['type' => true,'data' => $json['data']];
                    $call->query("UPDATE provider SET balance = '".$json['data']['balance']."' WHERE code = '$code'");
                }
                if($json['result'] == false) $res = ['type' => false,'msg' => $json['message']];
            } else {
                $res = ['type' => false,'msg' => 'Connection Failed!'];
            }
        } else if($code == 'MEDAN') {
            $json = $curl->connectPost($data['link'].'profile',['api_id' => $uid,'api_key' => $ukey]);
            if(isset($json['status'])) {
                if($json['status'] == true) {
                    $res = ['type' => true,'data' => $json['data']];
                    $call->query("UPDATE provider SET balance = '".$json['data']['balance']."' WHERE code = '$code'");
                }
                if($json['status'] == false) $res = ['type' => false,'msg' => $json['data']];
            } else {
                $res = ['type' => false,'msg' => 'Connection Failed!'];
            }
        } else if($code == 'ATL') {
            $json = $curl->connectPost($data['link'].'get_profile',['api_key' => $ukey]);
            if(isset($json['status'])) {
                if($json['status'] == true) {
                    $res = ['type' => true,'data' => $json['data']];
                    $call->query("UPDATE provider SET balance = '".$json['data']['balance']."' WHERE code = '$code'");
                }
                if($json['status'] == false) $res = ['type' => false,'msg' => $json['message']];
            } else {
                $res = ['type' => false,'msg' => 'Connection Failed!'];
            }
        } else {
            $res = ['type' => false,'msg' => 'Connection not found!'];
        }           
    } else {
        $res = ['type' => false,'msg' => 'Provider not registered!'];
    }
    return $res;
}

if(isset($_POST['save'])) {
    $web_token = base64_decode($_POST['web_token']);
    $api_id = (isset($_POST['userid'])) ? filter($_POST['userid']) : '-';
    $apikey = (!empty($_POST['apikey'])) ? filter($_POST['apikey']) : '';
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
    } else {
        $try = (!$apikey) ? ['type' => true] : ShennCheck($web_token,$api_id,$apikey);
        if($try['type'] == true) {
            if($call->query("UPDATE provider SET userid = '$api_id', apikey = '$apikey' WHERE code = '$web_token'") == true) {
                ShennXit(['type' => true,'message' => 'Connection success, data updated.']);
            } else {
                ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
            }
        } else {
            ShennXit(['type' => false,'message' => $try['msg']]);
        }
    }
}