<?php
require __DIR__.'/../_guard.php';
set_time_limit(240);
require '../../../connect.php';
require '../helper.php';

$prov = $call->query("SELECT * FROM provider WHERE code = 'ATL'");
$shows = isset($_GET['cjorg']) ? false : true;
if($prov->num_rows == 1) {
    $prov = $prov->fetch_assoc();
    $try = $curl->connectPost($prov['link'].'layanan/price_list',['api_key' => $prov['apikey'],'type' => 'pascabayar']);
    if(isset($try['status'])) {
        if($try['status'] == true) {
            if(count($try['data']) > 0) {
                for($i = 0; $i <= count($try['data'])-1; $i++) {
                    $data = $try['data'][$i];
                    $code = $data['code']; // CODE
                    $name = $data['layanan']; // LAYANAN
                    $note = $data['note']; // NOTE
                    $type = $data['type']; // TYPE
                    $brand = $data['category']; // CATEGORY
                    $price = $data['admin']+$data['komisi']; // PRICE
                    $priceb = 200; // PRICEB
                    $pricep = 100; // PRICEP
                    $status = $data['status']; // STATUS
                    $prepost = 'postpaid'; // PREPOST
                    
                    $check_category = $call->query("SELECT * FROM category WHERE code = '$brand' AND `order` = '$prepost'");
                    $check_service = $call->query("SELECT * FROM srv_ppob WHERE code = '$code' AND provider = 'ATL'");

                    if($check_category->num_rows == 0)
                        $call->query("INSERT INTO category VALUES ('','$brand','$brand','$type','$otype','$prepost')");
                            
                    if($check_service->num_rows == 0) {
                        $call->query("INSERT INTO srv_ppob VALUES ('','$type','$code','$name','$note','$price','$priceb','$pricep','$status','$brand','ATL','$date $time')");
                        if($shows == true) print '<font color="green"><pre>';
                        if($shows == true) print "[+] $name {Berhasil ditambahkan}<br>";
                        if($shows == true) print "Status: $status<br>";
                        if($shows == true) print "Harga Pusat: $price<br>";
                        if($shows == true) print "Harga Basic: ".($price+$priceb)."<br>";
                        if($shows == true) print "Harga Premium: ".($price+$pricep)."<br>";
                        if($shows == true) print "Harga Special: ".($price+$prices);
                        if($shows == true) print '</pre></font><hr>';
                    } else {
                        $data_service = $check_service->fetch_assoc();
                        if($data_service['price'] <> $price || $data_service['basic'] <> $priceb || $data_service['premium'] <> $pricep || $data_service['status'] <> $status) {
                            $call->query("UPDATE srv_ppob SET note = '$note', price = '$price', basic = '$priceb', premium = '$pricep', status = '$status', date_up = '$date $time' WHERE code = '$code' AND provider = 'ATL'");
                            if($shows == true) print '<font color="green"><pre>';
                            if($shows == true) print "[+] $name {Berhasil diupdate}<br>";
                            if($shows == true) print "Status: ".$data_service['status']." -> $status<br>";
                            if($shows == true) print "Harga Pusat: ".$data_service['price']." -> $price<br>";
                            if($shows == true) print "Harga Basic: ".($data_service['price']+$data_service['basic'])." -> ".($price+$data_service['basic'])."<br>";
                            if($shows == true) print "Harga Premium: ".($data_service['price']+$data_service['premium'])." -> ".($price+$data_service['premium']);
                            if($shows == true) print '</pre></font><hr>';
                        } else {
                            if($shows == true) print '<font color="red"><pre>[!] '.$name.' {Data masih sama}</pre></font><hr>';
                        }
                    }
                }
            } else {
                print '<font color="red"><pre>[!] No Service</pre></font>';
            }
        } else {
            print '<font color="red"><pre>[!] '.$try['message'].'</pre></font>';
        }
    } else {
        print '<font color="red"><pre>[!] Connection Failed</pre></font>';
    }
} else {
    print '<font color="red"><pre>[!] Provider not found</pre></font>';
}
?>