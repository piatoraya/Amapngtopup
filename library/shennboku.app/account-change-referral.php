<?php
if(!isset($call)) die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");
if(isset($_GET['send']) && $_POST) {
    $old_referral = $data_user['referral'];
    $new_referral = filter($_POST['u_referral']);
    
    if($result_csrf == false) {
        print sessResult(false, 'System Error, please try again later.');
    } else if($_CONFIG['lock']['status'] == true) {
        print sessResult(false, $_CONFIG['lock']['reason']);
    } else if($old_referral == $new_referral) {
        print sessResult(false, 'You didn\'t change your referral number.');
    } else if($json_user['new_referral'] == $new_referral) {
        print sessResult(false, 'Please re-open this modal to resend code.');
    } else if($call->query("SELECT referral FROM users WHERE referral = '$new_referral' AND username != '$sess_username'")->num_rows == 1) {
        print sessResult(false, 'Referral number has been registered.');
    } else if($call->query("SELECT referral FROM users WHERE info LIKE '%\"new_referral\": \"$new_referral\"%' AND username != '$sess_username'")->num_rows == 1) {
        print sessResult(false, 'Referral number has been registered on change.');
    } else {
        $New_OTP = random_number(6);
        $json_user['new_referral'] = $new_referral;
        $json_user['otp_referral']['code'] = $New_OTP;
        $json_user['otp_referral']['date_create'] = $dtme;
        $json_user['otp_referral']['date_resend'] = $dtme;
        $json_user['otp_referral']['date_expired'] = rdate('Y-m-d H:i:s', '+1 days');
        $json_user['otp_referral']['count_resend'] = 0;
        
        $waValid = (!empty(conf('atlantic-cfg', 4))) ? true : false;
        $send['result'] = ($waValid == true) ? @$WATL->sendMessage($new_referral, "*Halo $sess_username*\n*Kode OTP anda adalah* ```$New_OTP```\n*- ".$_CONFIG['title']."*")['result'] : mailer($_MAILER, [
            'dest' => $data_user['email'],
            'name' => $data_user['name'],
            'subject' => 'Code for '.$_CONFIG['title'],
            'message' => base64_encode(file_get_contents(base_url('library/layout-mail/change-referral?name='.urlencode($data_user['name']).'&code='.$New_OTP))),
            'is_template' => 'yes'
        ]);
        
        if($send['result'] == true) {
            if($call->query("UPDATE users SET info = '".json_encode($json_user)."' WHERE username = '$sess_username'") == true) {
                $waMsg = ($waValid == true) ? 'WhatsApp' : 'Email Address';
                print sessResult(true, 'The confirmation code has been successfully sent to your '.$waMsg.'.');
            } else {
                print sessResult(false, 'Our server is in trouble, please try again later.');
            }
        } else {
            print sessResult(false, 'Failed to Send OTP, please try again later.');
        }
    }
    die;
}
    
if(isset($_GET['resend']) && $_POST) {
    if($result_csrf == false) {
        print sessResult(false, 'System Error, please try again later.');
    } else if($_CONFIG['lock']['status'] == true) {
        print sessResult(false, $_CONFIG['lock']['reason']);
    } else if(strtotime($json_user['otp_referral']['date_expired']) < strtotime($dtme)) {
        print sessResult(false, 'The code has exceeded the time limit, please repeat the request for a change of referral number.');
    } else if($json_user['otp_referral']['count_resend'] >= 3) {
        print sessResult(false, 'The limit for resending the code is 3 times.');
    } else if(date_diffs($json_user['otp_referral']['date_resend'],$dtme,'minute') < 3) {
        print sessResult(false, 'Resend code can only be done in '.(3 - date_diffs($json_user['otp_referral']['date_resend'],$dtme,'minute')).' minutes.');
    } else {
        $json_user['otp_referral']['date_resend'] = $dtme;
        $json_user['otp_referral']['count_resend'] += 1;
        
        $waValid = (!empty(conf('atlantic-cfg', 4))) ? true : false;
        $send['result'] = ($waValid == true) ? @$WATL->sendMessage($new_referral, "*Halo $sess_username*\n*Kode OTP anda adalah* ```".$json_user['otp_referral']['code']."```\n*- ".$_CONFIG['title']."*")['result'] : mailer($_MAILER, [
            'dest' => $data_user['email'],
            'name' => $data_user['name'],
            'subject' => 'Code for '.$_CONFIG['title'],
            'message' => base64_encode(file_get_contents(base_url('library/layout-mail/change-referral?name='.urlencode($data_user['name']).'&code='.$json_user['otp_referral']['code']))),
            'is_template' => 'yes'
        ]);
        
        if($send['result'] == true) {
            $call->query("UPDATE users SET info = '".json_encode($json_user)."' WHERE username = '$sess_username'");
            $waMsg = ($waValid == true) ? 'WhatsApp' : 'Email Address';
            print sessResult(true, 'The confirmation code has been successfully sent to your '.$waMsg.'.');
        } else {
            print sessResult(false, 'Failed to Send OTP, please try again later.');
        }
    }
    die;
}
    
if(isset($_GET['cancel']) && $_POST) {
    if($result_csrf == false) {
        print sessResult(false, 'System Error, please try again later.');
    } else if($_CONFIG['lock']['status'] == true) {
        print sessResult(false, $_CONFIG['lock']['reason']);
    } else if($json_user['old_referral'] == $json_user['new_referral']) {
        print sessResult(false, 'Referral number change already canceled.');
    } else {
        $json_user['new_referral'] = $data_user['referral'];
        if($call->query("UPDATE users SET info = '".json_encode($json_user)."' WHERE username = '$sess_username'") == true) {
            print sessResult(true, 'Referral number change was canceled successfully.');
        } else {
            print sessResult(false, 'Our server is in trouble, please try again later.');
        }
    }
    die;
}
    
if(isset($_GET['confirm']) && $_POST) {
    $cotp = filter($_POST['c_referral']);
    if($result_csrf == false) {
        print sessResult(false, 'System Error, please try again later.');
    } else if($_CONFIG['lock']['status'] == true) {
        print sessResult(false, $_CONFIG['lock']['reason']);
    } else if(strtotime($json_user['otp_referral']['date_expired']) < strtotime($dtme)) {
        print sessResult(false, 'The code has exceeded the time limit, please repeat the request for a change of referral number.');
    } else if($cotp <> $json_user['otp_referral']['code']) {
        print sessResult(false, 'The code entered is wrong.');
    } else {
        $NReferral = $json_user['new_referral'];
        $json_user['old_referral'] = $NReferral;
        
        if($call->query("UPDATE users SET referral = '$NReferral', info = '".json_encode($json_user)."' WHERE username = '$sess_username'") == true) {
            print sessResult(true, 'Referral number change was successfully made.');
        } else {
            print sessResult(false, 'Our server is in trouble, please try again later.');
        }
    }
    die;
}