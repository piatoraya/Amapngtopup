<?php
if(!isset($call)) die("You cannot directly connect your application to the ShennBoku App system!<br>- Afdhalul Ichsan Yourdan");
if(isset($_POST['save'])) {
    $pst1 = filter($_POST['title']);
    $pst2 = filter($_POST['navbar']);
    $pst3 = filter($_POST['description']);
    $pst4 = filter($_POST['keyword']);
    $pst5 = filter($_POST['banner']);
    $pst6 = filter($_POST['icon']);
    $pst7 = filter($_POST['recsite']);
    $pst8 = filter($_POST['recsecret']);
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
    } else if(!$pst1 || !$pst2) {
        ShennXit(['type' => false,'message' => 'There is still a blank form.']);
    } else {
        if($call->query("UPDATE conf SET c1 = '$pst1', c2 = '$pst2', c3 = '$pst3', c4 = '$pst4', c5 = '$pst5', c6 = '$pst6', c7 = '$pst7', c8 = '$pst8' WHERE code = 'webcfg'") == true) {
            ShennXit(['type' => true,'message' => 'Website updated.']);
        } else {
            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
        }
    }
} if(isset($_POST['save-smtp'])) {
    $post_1 = filter($_POST['mailer_host']);
    $post_2 = filter($_POST['mailer_user']);
    $post_3 = filter($_POST['mailer_pass']);
    $post_4 = filter($_POST['mailer_from']);
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
    } else {
        if($call->query("UPDATE conf SET c1 = '$post_1', c2 = '$post_2', c3 = '$post_3', c4 = '$post_4' WHERE code = 'mailer'") == true) {
            ShennXit(['type' => true,'message' => 'SMTP Mailer updated.']);
        } else {
            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
        }
    }
} if(isset($_POST['save-hold'])) {
    $post_1 = filter($_POST['hold_basic']);
    $post_2 = filter($_POST['hold_premium']);
    $post_3 = filter($_POST['hold_admin']);

    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
    } else if(!is_numeric($post_1) || !is_numeric($post_2) || !is_numeric($post_3)) {
        ShennXit(['type' => false,'message' => 'Value must be numeric.']);
    } else if($post_1 < 0 || $post_2 < 0 || $post_3 < 0) {
        ShennXit(['type' => false,'message' => 'The minimum value is 0.']);
    } else {
        if($call->query("UPDATE conf SET c1 = '$post_1', c2 = '$post_2', c3 = '$post_3' WHERE code = 'hold-balance'") == true) {
            ShennXit(['type' => true,'message' => 'Hold Balance updated.']);
        } else {
            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
        }
    }
} if(isset($_POST['save-media'])) {
    $post_1 = filter($_POST['social_fb']);
    $post_2 = filter($_POST['social_ig']);
    
    if($result_csrf == false) {
        ShennXit(['type' => false,'message' => 'System Error, please try again later.']);
    } else if($_CONFIG['lock']['status'] == true) {
        ShennXit(['type' => false,'message' => $_CONFIG['lock']['reason']]);
    } else if(!in_array($data_user['level'],['Admin'])) {
        ShennXit(['type' => false,'message' => 'You do not have access to use this feature.']);
    } else {
        if($call->query("UPDATE conf SET c1 = '$post_1', c2 = '$post_2' WHERE code = 'webscmd'") == true) {
            ShennXit(['type' => true,'message' => 'Social Media updated.']);
        } else {
            ShennXit(['type' => false,'message' => 'Our server is in trouble, please try again later.']);
        }
    }
}