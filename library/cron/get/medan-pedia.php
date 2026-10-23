<?php
require __DIR__.'/../_guard.php';
set_time_limit(240);
require '../../../connect.php';
require '../helper.php';

$prov = $call->query("SELECT * FROM provider WHERE code = 'MEDAN'");
if($prov->num_rows == 1) {
    $prov = $prov->fetch_assoc();
    $try = $curl->connectPost($prov['link'].'services',['api_id' => $prov['userid'],'api_key' => $prov['apikey']]);
    if(isset($try['status'])) {
        if($try['status'] == true) {
            if(count($try['data']) > 0) {
                for($i = 0; $i <= count($try['data'])-1; $i++) {
                    $data = $try['data'][$i];
                    $pid = $data['id'];
                    $min = $data['min'];
                    $max = $data['max'];
                    $name = $data['name'];
                    $note = $data['description'];
                    $price = $data['price'];
                    $priceb = $price * 0.15;
                    $pricep  = $price * 0.10;
                    $category = $data['category'];
                    $category2 = str_replace(['&','*'],'',$data['category']);

                    $check_category = $call->query("SELECT * FROM category WHERE code = '$category2' AND type = 'social-media' AND `order` = 'social'");
                    $check_service = $call->query("SELECT * FROM srv_socmed WHERE pid = '$pid' AND provider = 'MEDAN'");

                    if($check_category->num_rows == 0)
                        $call->query("INSERT INTO category VALUES ('','$category2','$category','social-media','social-media','social')");
                            
                    if($check_service->num_rows == 0) {
                        $call->query("INSERT INTO srv_socmed VALUES ('','$pid','$category2','$name','$note','$price','$priceb','$pricep','$min','$max','available','MEDAN','$date $time')");
                        print '<font color="green"><pre>';
                        print "[+] $name {Berhasil ditambahkan}<br>";
                        print "Min: $min<br>";
                        print "Max: $max<br>";
                        print "Harga Pusat: $price<br>";
                        print "Harga Basic: ".($price+$priceb)."<br>";
                        print "Harga Premium: ".($price+$pricep);
                        print '</pre></font><hr>';
                    } else {
                        $data_service = $check_service->fetch_assoc();
                        if($data_service['price'] <> $price || $data_service['basic'] <> $priceb || $data_service['premium'] <> $pricep || $data_service['min'] <> $min || $data_service['max'] <> $max) {
                            $call->query("UPDATE srv_socmed SET note = '$note', price = '$price', basic = '$priceb', premium = '$pricep', min = '$min', max = '$max', date_up = '$date $time' WHERE pid = '$pid' AND provider = 'MEDAN'");
                            print '<font color="green"><pre>';
                            print "[+] $name {Berhasil diupdate}<br>";
                            print "Min: ".$data_service['min']." -> $min<br>";
                            print "Max: ".$data_service['max']." -> $max<br>";
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
            print '<font color="red"><pre>[!] '.$try['data'].'</pre></font>';
        }
    } else {
        print '<font color="red"><pre>[!] Connection Failed</pre></font>';
    }
} else {
    print '<font color="red"><pre>[!] Provider not found</pre></font>';
}
?>