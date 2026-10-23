<?php
class helper
{
    public function status($x) {
        $y = strtolower($x);
        if(in_array($y,['failed','gagal','error','refund','cancel'])) $str = 'error';
        if(in_array($y,['partial'])) $str = 'partial';
        if(in_array($y,['pending','waiting'])) $str = 'waiting';
        if(in_array($y,['processing','proses'])) $str = 'processing';
        if(in_array($y,['success','sukses','berhasil'])) $str = 'success';
        return (!isset($str)) ? 'waiting' : $str;
    }
    
    public function stock($x) {
        $available = ['available','active','normal'];
        return in_array(strtolower($x),$available) ? 'available' : 'empty';
    }
    
    public function space($x) {
        return preg_replace('/\s+/',' ',$x);
    }
    
    public function filter_type($x,$z = '') {
        $Type   = 'not-filtered';
        $type1  = ['PULSA','PULSA-REGULER','PULSA-TRANSFER','PULSA REGULER','PULSA TRANSFER'];
        $type2  = ['CHINA TOPUP','MALAYSIA TOPUP','PHILIPPINES TOPUP','SINGAPORE TOPUP','THAILAND TOPUP','VIETNAM TOPUP','PULSA-INTERNASIONAL'];
        $type3  = ['DATA','PAKET-INTERNET','DATA INTERNET'];
        $type4  = ['PAKET SMS & TELPON','PAKET-TELEPON'];
        $type5  = ['PLN','TOKEN-PLN'];
        $type6  = ['E-MONEY','SALDO-EMONEY'];
        $type7  = ['VOUCHER','PAKET-LAINNYA'];
        $type8  = ['STREAMING','TV','STREAMING-TV'];
        $type9  = ['GAMES','VOUCHER-GAME'];
        $type10 = ['PASCABAYAR'];
        $type11 = ['AKUN-PREMIUM','AKUN PREMIUM'];
        $type12 = ['MASA AKTIF','MASA-AKTIF'];
        
        if(in_array(strtoupper($x),$type1)) $Type = (stristr(strtolower($z),'transfer')) ? 'pulsa-transfer' : 'pulsa-reguler';
        if(in_array(strtoupper($x),$type2)) $Type = 'pulsa-internasional';
        if(in_array(strtoupper($x),$type3)) $Type = 'paket-internet';
        if(in_array(strtoupper($x),$type4)) $Type = 'paket-telepon';
        if(in_array(strtoupper($x),$type5)) $Type = 'token-pln';
        if(in_array(strtoupper($x),$type6)) $Type = 'saldo-emoney';
        if(in_array(strtoupper($x),$type7)) $Type = 'paket-lainnya';
        if(in_array(strtoupper($x),$type8)) $Type = 'streaming-tv';
        if(in_array(strtoupper($x),$type9)) $Type = 'voucher-game';
        if(in_array(strtoupper($x),$type10)) $Type = 'pascabayar';
        if(in_array(strtoupper($x),$type11)) $Type = 'akun-premium';
        if(in_array(strtoupper($x),$type12)) $Type = 'masa-aktif';
        return $Type;
    }
    
    public function AtlanticService($data, $gp = '') {
        for($i = 0; $i <= count($data)-1; $i++) {
            $out[$i]['brand']    = $data[$i]['provider'];
            // $out[$i]['category'] = $data[$i]['category'];
            $out[$i]['otype']    = $data[$i]['category'];
            $out[$i]['type']     = $this->filter_type($data[$i]['category'],$data[$i]['name']);
            $out[$i]['name']     = $this->space($data[$i]['name']);
            $out[$i]['note']     = $this->space($data[$i]['note']);
            $out[$i]['code']     = str_replace(['&','*'],'',$data[$i]['code']);
            $out[$i]['price']    = $data[$i]['price'];
            $out[$i]['status']   = $this->stock($data[$i]['status']);
            $out[$i]['prepost']  = 'prepaid';
        }
        return $out;
    }
}

$helper = new helper;