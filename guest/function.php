<?php
function curl_atlantic($end_point, $apikey, $print = false) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $end_point,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'api_key='.$apikey,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return ($print == false) ? json_decode($response, true) : $response;
}