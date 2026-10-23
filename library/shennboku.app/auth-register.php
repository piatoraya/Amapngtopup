<?php
if(!isset($call)) die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");

$closeRegist = conf('xtra-fitur',8);

if(isset($_POST['sendOTP'])) {
    $pst_mail = isset($_POST['mail']) ? filter($_POST['mail']) : '';
    $pst_name = isset($_POST['name']) ? ucwords(strtolower(filter($_POST['name']))) : '';
    $pst_user = isset($_POST['user']) ? filter($_POST['user']) : '';
    $pst_phone = isset($_POST['phone']) ? filter_phone('62',$_POST['phone']) : '';
    $pst_pin = isset($_POST['pin']) ? filter($_POST['pin']) : '';
    $pst_pas1 = isset($_POST['pas1']) ? filter($_POST['pas1']) : '';
    $pst_pas2 = isset($_POST['pas2']) ? filter($_POST['pas2']) : '';
    $pst_code = isset($_POST['code']) ? filter($_POST['code']) : '';
    
    $expMail = explode('@', $pst_mail);
    $accMail = ['gmail.com','yahoo.com','outlook.com','icloud.com'];
    
    $gcaptcha_use = (!$_CONFIG['reCAPTCHA']['secret'] || !$_CONFIG['reCAPTCHA']['site']) ? false : true;
    if($gcaptcha_use == true) $gcaptcha = $curl->connectPost('https://www.google.com/recaptcha/api/siteverify',[
        'secret' => $_CONFIG['reCAPTCHA']['secret'],
        'response' => $_POST['g-recaptcha-response'],
        'remoteip' => $client_ip,
    ]);
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($closeRegist == 'true') {
        ShennXit(['type' => false,'message' => 'Registration is closed.']);
    } else if($_CONFIG['mt']['web'] == 'true') {
        ShennXit(['type' => false,'message' => 'There is a website system maintenance, please try again later.']);
    } else if($gcaptcha_use == true && $gcaptcha['success'] == false) {
        ShennXit(['type' => false,'message' => 'Invalid reCaptcha.']);
    } else if(!isset($expMail[1]) || !$pst_name || !$pst_user || !$pst_phone || !$pst_pin || !$pst_pas1 || !$pst_pas2) {
        ShennXit(['type' => false,'message' => 'There is still a blank form.']);
    } else if(!in_array($expMail[1], $accMail)) {
        ShennXit(['type' => false,'message' => 'You cannot register using this email.']);
    } else if($pst_pas1 <> $pst_pas2) {
        ShennXit(['type' => false,'message' => 'Password confirmation does not match.']);
    } else if(strlen($pst_pin) != 6 || !is_numeric($pst_pin)) {
        ShennXit(['type' => false,'message' => 'The pin must contain a 6 digit number.']);
    } else if($_POST['accepttos'] != 'true') {
        ShennXit(['type' => false,'message' => 'Do you agree to the Terms of Service.']);
    } else if($call->query("SELECT id FROM users WHERE username = '$pst_user'")->num_rows == 1) {
        ShennXit(['type' => false,'message' => 'Username has been registered.']);
    } else if($call->query("SELECT phone FROM users WHERE phone = '$pst_phone'")->num_rows == 1) {
        ShennXit(['type' => false,'message' => 'Phone number has been registered.']);
    } else if($call->query("SELECT phone FROM users WHERE info LIKE '%\"new_phone\": \"$pst_phone\"%'")->num_rows == 1) {
        ShennXit(['type' => false,'message' => 'Phone number has been registered on change.']);
    } else if($call->query("SELECT email FROM users WHERE email = '$pst_mail'")->num_rows == 1) {
        ShennXit(['type' => false,'message' => 'Email address has been registered.']);
    } else if($call->query("SELECT email FROM users WHERE info LIKE '%\"new_email\": \"$pst_mail\"%'")->num_rows == 1) {
        ShennXit(['type' => false,'message' => 'Email address has been registered on change.']);
    } else {
        $pst_name = ucwords(strtolower(preg_replace('/[^A-Za-z ]/', '', $pst_name)));
        $pst_user = ucwords(strtolower(preg_replace('/[^A-Za-z0-9 ]/', '', $pst_user)));
        $uplink = $call->query("SELECT id FROM users WHERE referral = '$pst_code'")->num_rows == 1
                    ? $pst_code : 'SYSTEM';
        
        $waValid = (!empty(conf('atlantic-cfg', 4))) ? true : false;
        $_SESSION['temp']['whatsapp'] = ($waValid == true) ? random_number(6) : false;
        $_SESSION['temp']['email'] = random(12);
        $_SESSION['temp']['data'] = [
            'name' => $pst_name,
            'email' => $pst_mail,
            'phone' => $pst_phone,
            'username' => $pst_user,
            'password' => bcrypt($pst_pas1),
            'secure' => bcrypt($pst_pin),
            'uplink' => $uplink
        ];
        
        $send_mail = mailer($_MAILER, [
            'dest' => $pst_mail,
            'name' => $pst_name,
            'subject' => 'Code for '.$_CONFIG['title'],
            'message' => base64_encode(file_get_contents(base_url('library/layout-mail/confirm-email?name='.urlencode($pst_name).'&code='.$_SESSION['temp']['email']))),
            'is_template' => 'yes'
        ]);
        $send_whats = ($send_mail == true) ? (($waValid == false) ? ['result' => true] : $WATL->sendMessage($pst_phone, "*Halo $pst_user*\n*Kode OTP anda adalah* ```".$_SESSION['temp']['whatsapp']."```\n*- ".$_CONFIG['title']."*")) : ['result' => false];
        
        if($send_mail == true && $send_whats['result'] == true) {
            $msg = ($waValid == true) ? ' and whatsapp' : '';
            ShennXit(['type' => true,'message' => 'OTP has been sent successfully, please check your inbox or spam email'.$msg.'.']);
        } else {
            unset($_SESSION['temp']);
            ShennXit(['type' => false,'message' => 'Failed to send OTP.']);
        }
    }
} if(isset($_SESSION['temp']) && isset($_POST['confirm'])) {
    $pst2 = $_SESSION['temp']['data']['name'];
    $pst3 = $_SESSION['temp']['data']['email'];
    $pst4 = $_SESSION['temp']['data']['phone'];
    $pst5 = $_SESSION['temp']['data']['username'];
    $pst6 = $_SESSION['temp']['data']['password'];
    $pst7 = $_SESSION['temp']['data']['secure'];
    $pst12 = $_SESSION['temp']['data']['uplink'];
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($closeRegist == 'true') {
        ShennXit(['type' => false,'message' => 'Registration is closed.']);
    } else if($_CONFIG['mt']['web'] == 'true') {
        ShennXit(['type' => false,'message' => 'There is a website system maintenance, please try again later.']);
    } else if($_SESSION['temp']['whatsapp'] != false && $_SESSION['temp']['whatsapp'] <> $_POST['otpwa']) {
        ShennXit(['type' => false,'message' => 'Incorrect phone number verification code.']);
    } else if($_SESSION['temp']['email'] <> $_POST['otpgm']) {
        ShennXit(['type' => false,'message' => 'Email address verification code does not match.']);
    } else {
        $user_env = json_encode([
            'old_email' => $pst3,       'new_email' => $pst3,
            'otp_email' => ['code' => rand(111111,999999),'date_create' => date('Y-m-d H:i:s'),'date_resend' => date('Y-m-d H:i:s'),'date_expired' => rdate('Y-m-d H:i:s', '+3 days'),'count_resend' => 0],
            'old_phone' => $pst4,       'new_phone' => $pst4,
            'otp_phone' => ['code' => rand(111111,999999),'date_create' => date('Y-m-d H:i:s'),'date_resend' => date('Y-m-d H:i:s'),'date_expired' => rdate('Y-m-d H:i:s', '+3 days'),'count_resend' => 0],
            'change_password' => ['code' => rand(111111,999999),'date_create' => date('Y-m-d H:i:s'),'date_expired' => rdate('Y-m-d H:i:s', '+1 days')],
            'store' => ['name' => $pst2],
        ]);
        
        if($call->query("INSERT INTO users VALUES (null, '$pst2', '$pst3', '$pst4', '$pst5', '$pst6', '$pst7', '0', '0', 'Basic', '".random(16)."', '$pst12', 'active', '$dtme', '$user_env', '".sha1(sha1(strtotime(date('Y-m-d H:i:s'))))."')") == true) {
            $call->query("INSERT INTO logs VALUES (null, '$pst5', 'register', 'Welcome!', 'success', '$client_ip', '$client_iploc', '$dtme')");
            $call->query("INSERT INTO users_api VALUES ('$pst5', '".random(8)."', '".random(64)."', '', '', 'development')");
            unset($_SESSION['temp']);
            ShennXit(['type' => true,'message' => 'Your account has been registered successfully.']);
        } else {
            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
        }
    }
} if(isset($_SESSION['temp']) && isset($_POST['cancel'])) {
    unset($_SESSION['temp']);
    ShennXit(['type' => true,'message' => 'Canceled successfully.']);
}