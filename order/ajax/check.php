<?php 
require '../../connect.php';
require _DIR_('library/session/session');

function err($x,$y) {
    if($x == true) {
        return json_encode(['result' => true,'data' => $y]);
    } else {
        return json_encode(['result' => false,'message' => $y]);
    }
}

require _DIR_('library/function/GameChecker'); 
$GAME = new DuniaGames(); $GameNC = $GAME;
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && isset($data_user)) {
    if(!isset($_POST['target']) || !isset($_POST['category'])) exit(err(false,'No direct script access allowed!'));
    
    $c = isset($_POST['category']) ? filter($_POST['category']) : '';
    $id = isset($_POST['target']) ? filter($_POST['target']) : '';
    $id2 = isset($_POST['target2']) ? filter($_POST['target2']) : '';
       
    $see = $call->query("SELECT * FROM con_game WHERE code = '$c'")->fetch_assoc();
    
    /*
    if(substr($c,0,13) == 'Mobile Legend') {
        $checkNick = 'mobile_legends';
    } else if(substr($c,0,9) == 'Free Fire') {
        $checkNick = 'free_fire';
    } else {
        $checkNick = 'mobile_legends';
    }
    */
    
    $checkNick = $see['code_id'];  
    
    if($see['form'] == '2') { 
        if($c == $see['code']) $result = $GameNC->$checkNick($id,$id2);
        else exit(err(false,'The game is not registered, please contact the developer!'));
    } else {
        if($c == $see['code']) $result = $GameNC->$checkNick($id);
        else exit(err(false,'The game is not registered, please contact the developer!'));
    } 
    
    
    /*
    if($c == "FREE FIRE") $result = $GameNC->free_fire($id);
    
    // Zone only
    else if($c == "Mobile Legends") $result = $GameNC->mobile_legends($id,$id2);
    else if($c == "Mobile Legends A") $result = $GameNC->mobile_legends($id,$id2);
    else if($c == "Mobile Legends B") $result = $GameNC->mobile_legends($id,$id2);
    else if($c == "Mobile Legends C") $result = $GameNC->mobile_legends($id,$id2);
    else if($c == "Mobile Legends Vilog") $result = $GameNC->mobile_legends($id,$id2);
    else if($c == "Mobile Legends Membership") $result = $GameNC->mobile_legends($id,$id2);
    else if($c == "Mobile Legends Slow") $result = $GameNC->mobile_legends($id,$id2);
    // End zone
 
    else exit(err(false,'The game is not registered, please contact the developer!'));
    */
    
    
    
    
    if(isset($result)) {
        if($result == true) {
            exit(err(true,$result));
        } else {
            exit(err(false,'Player not found'));
        }
    } else {
        exit(err(false,'Connetion Failed!'));
    }
    
    /*
    if(isset($result['result'])) {
        if($result['result'] == true) {
            if(isset($result['data']['nick_game'])) {
                exit(err(true,$result['data']['nick_game']));
            } else {
                exit(err(false,'Player not found!'));
            }
        } else {
            exit(err(false,$result['message']));
        }
    } else {
        exit(err(false,'Connection Failed!'));
    } */
    
} else {
    exit(err(false,'No direct script access allowed!'));
}