<?php

class DuniaGames
{
    const API_URL = "https://api.duniagames.co.id/api/transaction/v1/top-up/inquiry/store";
    
    public function MOBILE_LEGENDS($userID, $zoneID)
    {
        $payload       = '{"productId":1,"itemId":66,"catalogId":121,"paymentId":805,"gameId":"' . $userID . '","zoneId":"' . $zoneID . '","product_ref":"CMS","product_ref_denom":"AE"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
    
    public function FREE_FIRE($gameID)
    {
        $payload       = '{"productId":3,"itemId":353,"catalogId":376,"paymentId":1252,"gameId":"' . $gameID . '","product_ref":"CMS","product_ref_denom":"REG"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json          = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
    
    public function COD_MOBILE($openID)
    {
        $payload       = '{"productId":18,"itemId":88,"catalogId":144,"paymentId":828,"gameId":"' . $openID . '","product_ref":"CMS","product_ref_denom":"REG"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json          = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
   
    public function LIFE_AFTER($gameID, $serverName)
    {
        $server        = array(
            'miskatown' => '500001',
            'sandcastle' => '500002',
            'mouthswamp' => '500003',
            'redwoodtown' => '500004',
            'obelisk' => '500005',
            'fallforest' => '510001',
            'mountsnow' => '510002',
            'nancycity' => '520001',
            'charlestown' => '520002',
            'snowhighlands' => '520003',
            'santopany' => '520004',
            'levincity' => '520005',
            'newland' => '500006',
            'milestone' => '520006'
        );
        $serverID      = $server[strtolower($serverName)];
        $payload       = '{"productId":21,"itemId":117,"catalogId":173,"paymentId":857,"gameId":"' . $gameID . '","serverId":"' . $serverID . '","product_ref":"REG","product_ref_denom":"REG"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json          = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
    
    /* Required: Custom Lann Digital */
    
    
    public function SPEED_DRIFTERS($gameID)
    {
        $payload = '{"productId":5,"itemId":26,"catalogId":81,"paymentId":2073,"gameId":"'.$gameID.'","product_ref":"REG","product_ref_denom":"REG"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json          = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
        
    public function POINT_BLANK($gameID)
    {
        $payload = '{"productId":17,"itemId":82,"catalogId":138,"paymentId":2099,"gameId":"'.$gameID.'","product_ref":"REG","product_ref_denom":"REG"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json          = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
    
    public function MARVEL_SUPER_WAR($gameID)
    {
        $payload = '{"productId":22,"itemId":127,"catalogId":183,"paymentId":867,"gameId":"'.$gameID.'","product_ref":"REG","product_ref_denom":"REG"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json          = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
    
    public function LOKA_PALA($gameID)
    {
        $payload = '{"productId":35,"itemId":259,"catalogId":315,"paymentId":1541,"gameId":"'.$gameID.'","product_ref":"REG","product_ref_denom":"REG"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json          = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
    
    public function ARENA_OF_VALOR($gameID)
    {
        $payload = '{"productId":4,"itemId":18,"catalogId":73,"paymentId":296,"gameId":"'.$gameID.'","product_ref":"REG","product_ref_denom":"REG"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json          = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
    
    public function LOST_SAGA_ORIGIN($gameId)
    {
        $payload = '{"productId":69,"itemId":560,"catalogId":587,"paymentId":1840,"gameId":"'.$gameId.'","serverId":"1","product_ref":"REG","product_ref_denom":"REG"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json          = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
    
    public function PERFECT_WORLD($gameId)
    {
        $payload = '{"productId":70,"itemId":567,"catalogId":593,"paymentId":1846,"gameId":"'.$gameId.'","serverId":"1","product_ref":"REG","product_ref_denom":"REG"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json          = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
    
    public function RF_REMASTERED($gameId)
    {
        $payload = '{"productId":74,"itemId":588,"catalogId":614,"paymentId":1867,"gameId":"'.$gameId.'","serverId":"1","product_ref":"REG","product_ref_denom":"REG"}';
        $checkUsername = $this->Request(self::API_URL, $payload);
        $json          = json_decode($checkUsername, true);
        return $json['data']['userNameGame'];
    }
    
    public function RF_CLASSIC($gameId)
    {
       $payload = '{"productId":73,"itemId":582,"catalogId":608,"paymentId":1861,"gameId":"'.$gameId.'","serverId":"1","product_ref":"REG","product_ref_denom":"REG"}';
       $checkUsername = $this->Request(self::API_URL, $payload);
       $json          = json_decode($checkUsername, true);
       return $json['data']['userNameGame'];
    }
    
    public function RAGNAROK_NEXT_GENERATION($gameId)
    {
       $payload = '{"productId":75,"itemId":682,"catalogId":701,"paymentId":2162,"gameId":"'.$gameId.'","product_ref":"REG","product_ref_denom":"REG"}';
       $checkUsername = $this->Request(self::API_URL, $payload);
       $json          = json_decode($checkUsername, true);
       return $json['data']['userNameGame'];
    }
    
    public function RAGNAROK_FOREVER_LOVE($gameId)
    {
       $payload = '{"productId":71,"itemId":573,"catalogId":599,"paymentId":1852,"gameId":"'.$gameId.'","serverId":"1","product_ref":"REG","product_ref_denom":"REG"}';
       $checkUsername = $this->Request(self::API_URL, $payload);
       $json          = json_decode($checkUsername, true);
       return $json['data']['userNameGame'];
    }
    
    public function SHELL_FIRE($gameId)
    {
       $payload = '{"productId":10,"itemId":47,"catalogId":102,"paymentId":786,"gameId":"'.$gameId.'","serverId":"1","product_ref":"REG","product_ref_denom":"REG"}';
       $checkUsername = $this->Request(self::API_URL, $payload);
       $json          = json_decode($checkUsername, true);
       return $json['data']['userNameGame'];
    }
    
    public function ATLANTICA_ONLINE($gameId)
    {
       $payload = '{"productId":62,"itemId":432,"catalogId":455,"paymentId":1624,"gameId":"'.$gameId.'","product_ref":"REG","product_ref_denom":"REG"}';
       $checkUsername = $this->Request(self::API_URL, $payload);
       $json          = json_decode($checkUsername, true);
       return $json['data']['userNameGame'];
    }
    
    public function ANGEL_SQUAD($gameId)
    {
       $payload = '{"productId":78,"itemId":720,"catalogId":1805,"paymentId":4515,"gameId":"'.$gameId.'","product_ref":"REG","product_ref_denom":"REG"}';
       $checkUsername = $this->Request(self::API_URL, $payload);
       $json          = json_decode($checkUsername, true);
       return $json['data']['userNameGame'];
    }
    
    protected function Request($url, $post = false)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true
        ));
        
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        
        $headers = array(
            'Host: api.duniagames.co.id',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:83.0) Gecko/20100101 Firefox/83.0',
            'Content-Type: application/json'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        #print $result;
        curl_close($ch);
        return $result;
    }
}