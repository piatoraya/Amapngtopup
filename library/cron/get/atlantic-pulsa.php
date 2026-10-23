<?php
require __DIR__.'/../_guard.php';
set_time_limit(240);
require '../../../connect.php';
require '../helper.php';

$prov = $call->query("SELECT * FROM provider WHERE code = 'ATL'");
if($prov->num_rows == 1) {
    $prov = $prov->fetch_assoc();
    $connect = $curl->connectPost($prov['link'].'layanan/price_list',['api_key' => $prov['apikey'],'type' => 'prabayar']);
    if(isset($connect['status'])) {
        if($connect['status'] == true) {
            if(count($connect['data']) > 0) {
                $try['data'] = $helper->AtlanticService($connect['data']);
                for($i = 0; $i <= count($try['data'])-1; $i++) {
                    $data = $try['data'][$i];
                    $code = $data['code'];
                    $name = $data['name'];
                    $note = $data['note'];
                    $type = $data['type'];
                    $otype = $data['otype'];
                    $brand = $data['brand'];
                    $price = $data['price'];
                    $priceb = 220;
                    $pricep = 120;
                    $status = $data['status'];
                    $prepost = strtolower($data['prepost']);
                    
                    $check_category = $call->query("SELECT * FROM category WHERE code = '$brand' AND type = '$type' AND `order` = '$prepost'");
                    $check_service = $call->query("SELECT * FROM srv_ppob WHERE code = '$code' AND provider = 'ATL'");

                    if($check_category->num_rows == 0)
                        $call->query("INSERT INTO category VALUES ('','$brand','$brand','$type','$otype','$prepost')");
                            
                    if($check_service->num_rows == 0) {
                        $call->query("INSERT INTO srv_ppob VALUES ('','$type','$code','$name','$note','$price','$priceb','$pricep','$status','$brand','ATL','$date $time')");
                        print '<font color="green"><pre>';
                        print "[+] $name {Berhasil ditambahkan}<br>";
                        print "Type: $otype -> $type<br>";
                        print "Status: $status<br>";
                        print "Harga Pusat: $price<br>";
                        print "Harga Basic: ".($price+$priceb)."<br>";
                        print "Harga Premium: ".($price+$pricep);
                        print '</pre></font><hr>';
                    } else {
                        $data_service = $check_service->fetch_assoc();
                        if($data_service['price'] <> $price || $data_service['basic'] <> $priceb || $data_service['premium'] <> $pricep || $data_service['status'] <> $status) {
                            $call->query("UPDATE srv_ppob SET note = '$note', price = '$price', basic = '$priceb', premium = '$pricep', status = '$status', date_up = '$date $time' WHERE code = '$code' AND provider = 'ATL'");
                            print '<font color="green"><pre>';
                            print "[+] $name {Berhasil diupdate}<br>";
                            print "Type: $otype -> $type<br>";
                            print "Status: ".$data_service['status']." -> $status<br>";
                            print "Harga Pusat: ".$data_service['price']." -> $price<br>";
                            print "Harga Basic: ".($data_service['price']+$data_service['basic'])." -> ".($price+$data_service['basic'])."<br>";
                            print "Harga Premium: ".($data_service['price']+$data_service['premium'])." -> ".($price+$data_service['premium']);
                            print '</pre></font><hr>';
                        } else {
                            print '<font color="red"><pre>[!] '.$name.' {Data masih sama}</pre></font><hr>';
                        }
                    }
                }
            } else {
                print '<font color="red"><pre>[!] No Service</pre></font>';
            }
        } else {
            print '<font color="red"><pre>[!] '.$connect['message'].'</pre></font>';
        }
    } else {
        print '<font color="red"><pre>[!] Connection Failed</pre></font>';
    }
} else {
    print '<font color="red"><pre>[!] Provider not found</pre></font>';
}
?>