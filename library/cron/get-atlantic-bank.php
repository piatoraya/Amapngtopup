<?php
require __DIR__.'/_guard.php';
set_time_limit(240);
require '../../connect.php';

$prov = $call->query("SELECT * FROM provider WHERE code = 'ATL'")->fetch_assoc();
$result = curl_atlantic($prov['link'].'transfer/bank_list',$prov['apikey']);
if(isset($result['status'])) {
    if($result['status'] == true) {
        if(count($result['data']) > 0) {
            for($i = 0; $i <= count($result['data'])-1; $i++) {
                $data = $result['data'][$i];
                $bank_code = $data['bank_code'];
                $bank_name = $data['bank_name'];
                $bank_type = 'bank';
                
                $search = $call->query("SELECT * FROM srv_bank WHERE name = '$bank_name'");
                if($search->num_rows == 0) {
                    $call->query("INSERT INTO srv_bank VALUES (null,'$bank_code','$bank_name','$dtme')");
                    print '<font color="green"><pre>';
                    print "[+] $bank_name {Berhasil ditambahkan}";
                    print "</pre></font>";
                } else {
                    $row = $search->fetch_assoc();
                    if($row['code'] <> $bank_code || $row['name'] <> $bank_name) {
                        $call->query("UPDATE srv_bank SET code = '$bank_code', name = '$bank_code', update_at = '$dtme' WHERE code = '$code'");
                        print '<font color="green"><pre>';
                        print "[~] $bank_name {Berhasil diperbarui}";
                        print "</pre></font>";
                    } else {
                        print '<font color="red"><pre>';
                        print "[!] $bank_name {Data masih sama}";
                        print "</pre></font>";
                    }
                }                   
            }
        } else {
            print '<font color="red"><pre>[!] No Service</pre></font>';
        }
    } else {
        print '<font color="red"><pre>[!] '.$result['message'].'</pre></font>';
    }
}
               
                    
                 
                
                



