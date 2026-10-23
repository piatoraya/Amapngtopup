<?php
class WapiSender
{
    private $apikey;
    public $device;
    public $dest;
    private $base_url = 'https://wapisender.id/api/v5';
    
    public function __construct($ukey, $subsid = '', $base = '') {
        $this->apikey = $ukey;
        $this->device = $subsid;
        $this->base_url = $base;
    }
    
    private function connect($x,$n = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $x['api_key'] = $this->apikey;
        $x['device_key'] = $this->device;
        if(substr($n,0,8) == '/message') $x[((strtolower(substr($dest,0,5)) == 'group') ? 'group_id' : 'destination')] = str_replace('group','',$this->dest);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($x));
        curl_setopt($ch, CURLOPT_URL, $this->base_url.$n);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = json_decode(curl_exec($ch), true);
        print curl_exec($ch);
        curl_close($ch);
        if(isset($result['status'])) {
            return ['result' => ($result['status'] == 'ok') ? true : false,'data' => (isset($result['data']) ? $result['data'] : null),'message' => $result['message']];
        } else {
            return ['result' => false, 'data' => null, 'message' => 'Connection Failed.'];
        }
    }
    
    public function sendMessage($phone,$msg) {
        $this->dest = $phone;
        return $this->connect(['message' => $msg], '/message/text');
    }

    public function sendImage($phone,$source,$capt) {
        $this->dest = $phone;
        return $this->connect(['image' => base64_encode(file_get_contents($source)),'caption' => $capt], '/message/image');
    }
    
    public function deviceInfo() {
        return $this->connect([], '/device/info');
    }
    
    public function deviceGroup() {
        return $this->connect([], '/group');
    }
    
    public function deviceShutdown() {
        return $this->connect([], '/device/stop');
    }
    
    public function deviceRestart() {
        return $this->connect([], '/device/start');
    }
}