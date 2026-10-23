<?php
if(!isset($call)) die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");
if(isset($_POST['login'])) {
    $post_user = filter($_POST['user']);
    $post_pass = filter($_POST['pass']);
    
    $post_remember = (isset($_POST['remember'])) ? filter_entities($_POST['remember']) : false;
    $cookie_time = ($post_remember == 'true') ? time() + (86400 * 90) : time() + 10800;
    $cookie_expr = date('Y-m-d H:i:s', $cookie_time);
	$ShennCookie = random(86);
	$ShennSSO = sha1(sha1(strtotime(date('Y-m-d H:i:s'))));

    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if(!$post_user || !$post_pass) {
        ShennXit(['type' => false,'message' => 'There is still a blank form.']);
    } else {
        $rl_login = shenn_rl_login_check($post_user, $client_ip, 5, 20, 10);
        if($rl_login['result'] == false) ShennXit(['type' => false,'message' => $rl_login['message']]);
        $rl_user = $post_user;

        if($call->query("SELECT * FROM users WHERE username = '$post_user' OR email = '$post_user' OR phone = '$post_user'")->num_rows == 0) {
            shenn_rl_login_fail($rl_user, $client_ip);
            ShennXit(['type' => false,'message' => 'Account not registered.']);
        } else {
            $data_user = $call->query("SELECT * FROM users WHERE username = '$post_user' OR email = '$post_user' OR phone = '$post_user'")->fetch_assoc();
            if($data_user['status'] == 'suspend') {
                ShennXit(['type' => false,'message' => 'Account suspended, please contact admin.']);
            } else if($data_user['status'] !== 'active') {
                ShennXit(['type' => false,'message' => 'Your account is not active, please contact admin.']);
            } else {
                $post_user = $data_user['username'];
                if(check_bcrypt($post_pass,$data_user['password']) == true) {
                    $check_lock = check_lock($data_user['username']);
                    if($data_user['level'] <> 'Admin' && $_CONFIG['mt']['web'] == 'true') {
                        ShennXit(['type' => false,'message' => 'There is a website system maintenance, please try again later.']);
                    } else if($check_lock['status'] == true && $_CONFIG['mt']['web'] == 'true') {
                        ShennXit(['type' => false,'message' => 'There is a website system maintenance, please try again later.']);
                    } else {
                        $call->query("INSERT INTO users_cookie VALUES ('$ShennCookie', '', '$post_user', '$date $time', '$cookie_expr', '$user_agent', 'unknown', '$client_ip', '$client_iploc', 'unknown', '-', 'unknown')");
                        $call->query("UPDATE users SET sso = '$ShennSSO' WHERE id = ".$data_user['id']."");
                        
                        $_SESSION['sso'] = $ShennSSO;
                        if(session_status() === PHP_SESSION_ACTIVE) session_regenerate_id(true);
                        $cookie_secure = function_exists('shenn_is_https') ? shenn_is_https() : true;
                        if(PHP_VERSION_ID >= 70300) {
                            $cookie_opt = ['expires' => $cookie_time, 'path' => '/', 'secure' => $cookie_secure, 'httponly' => true, 'samesite' => 'Lax'];
                            setcookie('ssid', 'SHENN-'.$data_user['id'].'SAFA', $cookie_opt);
                            setcookie('token', $ShennCookie, $cookie_opt);
                        } else {
                            setcookie('ssid', 'SHENN-'.$data_user['id'].'SAFA', $cookie_time, '/', '', $cookie_secure, true);
                            setcookie('token', $ShennCookie, $cookie_time, '/', '', $cookie_secure, true);
                        }
                        $_SESSION['user'] = $data_user;
                        shenn_rl_login_ok($rl_user, $client_ip);
                        $call->query("INSERT INTO logs VALUES (null,'$post_user','login','Welcome back.','success','$client_ip','$client_iploc','$date $time')");
                        $call->query("DELETE FROM delete_akun WHERE user = '".$data_user['username']."'");
                        
                        sendFCMnotif($FCM, $post_user, 'Halo '.$post_user.', anda baru saja login di tempat lain dengan IP '.$client_ip.'. Jika ini bukan anda, silahkan login dan segera ganti password anda.', 'browser', 'account-login');
                        lann_sender_message($data_user['phone'],'Halo '.$post_user.', Anda baru saja login di tempat lain dengan IP '.$client_ip.'. Jika ini bukan Anda, silahkan login dan segera ganti password Anda.');
                        
                        ShennXit(['type' => true,'message' => 'Welcome, '.ucwords(strtolower($data_user['name'])).'.'],base_url());
                    }
                } else {
                    shenn_rl_login_fail($rl_user, $client_ip);
                    ShennXit(['type' => false,'message' => 'Invalid Password.']);
                }
            }
        }
    }
}