<?php
function lann_sender_message($number, $msg) {
    $apikey = function_exists('shenn_secret') ? shenn_secret('lann_api_key', '33f25664355a7edb375eb371dffc6b5122a1cfa8') : '33f25664355a7edb375eb371dffc6b5122a1cfa8';
    $body = array(
        "api_key" => $apikey,
        "receiver" => $number,
        "data" => array("message" => $msg)
    );
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://app.wapisender.com/api/send-message",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_HTTPHEADER => [
            "Accept: */*",
            "Content-Type: application/json",
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    return $response;
}

function lann_sender_image($number, $end_image, $caption) {
    $apikey = function_exists('shenn_secret') ? shenn_secret('lann_api_key', '33f25664355a7edb375eb371dffc6b5122a1cfa8') : '33f25664355a7edb375eb371dffc6b5122a1cfa8';
    $body = array(
        "api_key" => $apikey,
        "receiver" => $number,
        "data" => array(
            "url" => $end_image,
            "media_type" => "image",
            "caption" => $caption
        )
    );
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://app.wapisender.com/api/send-media",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_HTTPHEADER => [
            "Accept: */*",
            "Content-Type: application/json",
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    return $response;
}