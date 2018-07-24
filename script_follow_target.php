<?php
$usernameig = "..."; // Ganti ... Dengan Username Instagram kalian
$passwordig = "..."; // Ganti ... Dengan Passowrd Instagram kalian
$targetig = "..."; // Ganti ... Dengan Target ID kalian
$tipeig = "followers"; // Pilih tipe Followers atau Following terserah kalian
$jumlahig = "..."; //   Jumlah yang ingin di follow 100-1000

/* TOLONG HARGAI SCRIPT MAKER */
/* Coded By kakatoji */
/* Instagram -> @kakatoji0 */
/* Website -> http://kakatoji.blogspot.com */

// Memulai API Instagram
set_time_limit(0);
ignore_user_abort(1);
    function proccess($ighost, $useragent, $url, $cookie = 0, $data = 0, $httpheader = array(), $proxy = 0){
        $url = $ighost ? 'https://i.instagram.com/api/v1/' . $url : $url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        if($proxy):
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
        endif;
        if($httpheader) curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if($cookie) curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        if ($data):
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        endif;
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch);
        if(!$httpcode) return false; else{
            $header = substr($response, 0, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
            curl_close($ch);
            return array($header, $body);
        }
    }
    function generate_useragent($sign_version = '6.22.0'){
        $resolusi = array('1080x1776','1080x1920','720x1280', '320x480', '480x800', '1024x768', '1280x720', '768x1024', '480x320');
        $versi = array('GT-N7000', 'SM-N9000', 'GT-I9220', 'GT-I9100');     $dpi = array('120', '160', '320', '240');
        $ver = $versi[array_rand($versi)];
        return 'Instagram '.$sign_version.' Android ('.mt_rand(10,11).'/'.mt_rand(1,3).'.'.mt_rand(3,5).'.'.mt_rand(0,5).'; '.$dpi[array_rand($dpi)].'; '.$resolusi[array_rand($resolusi)].'; samsung; '.$ver.'; '.$ver.'; smdkc210; en_US)';
    }
    function hook($data) {
        return 'ig_sig_key_version=4&signed_body=' . hash_hmac('sha256', $data, '469862b7e45f078550a0db3687f51ef03005573121a3a7e8d7f43eddb3584a36') . '.' . urlencode($data);
    }
    function generate_device_id(){
        return 'android-' . md5(rand(1000, 9999)).rand(2, 9);
    }
    function generate_guid($tipe = 0){
        $guid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(16384, 20479),
        mt_rand(32768, 49151),
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(0, 65535));
        return $tipe ? $guid : str_replace('-', '', $guid);
    }
    $ua = generate_useragent();
        $devid = generate_device_id();
        $login = proccess(1, $ua, 'accounts/login/', 0, hook('{"device_id":"'.$devid.'","guid":"'.generate_guid().'","username":"'.$usernameig.'","password":"'.$passwordig.'","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}'));
        $data = json_decode($login[1]);
        if($data->status != 'ok')
            print ' <html>
        <body>
        <center>Username / Password Instagrammu salah</center>';
        else{
            preg_match_all('%Set-Cookie: (.*?);%',$login[0],$d);$cookie = '';
            for($o=0;$o<count($d[0]);$o++)$cookie.=$d[1][$o].";";
            $_SESSION['data'] = array('cookies' => $cookie, 'useragent' => $ua, 'device_id' => $devid, 'username' => $data->logged_in_user->username, 'id' => $data->logged_in_user->pk);
             }
    // End Api Instagram
// Ini Fungsi Asli
$limit_unfollow = $jumlahig; // jumlah maksimal yang mau difollow , jika mau ngefollow semua daftar silahkan isi 'maksimal' ..pakai ' petik
$with_delay = 1; // jumlah delaynya
$target = $targetig ; // username target yang mau dilihat
$whitelist = array(); // tambahkan user yang tidak ingin difolow misalnya: array('id_instagram','id_instagram2');
$listaccounts = $_SESSION['data'];
$jenis = $tipeig;
if($jenis != 'followers'){
    $tipe = 'following';
} else { $tipe = 'followers'; }
$curl_ig=curl_init('https://www.instagram.com/'.$target.'/');
curl_setopt($curl_ig,CURLOPT_RETURNTRANSFER,1);
curl_setopt($curl_ig,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl_ig,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; rv:21.0) Gecko/20100101 Firefox/21.0");
$curl_user=curl_exec($curl_ig);$user=curl_getinfo($curl_ig);curl_close($curl_ig);
if($user['http_code']!==200) die('
       <body>
       <center>Username tidak tersedia</center>');
preg_match('#", "id": "(.*?)", "biography": "#',$curl_user,$id);
$target = $id[1];
        $getinfo = proccess(1, $listaccounts['useragent'], 'users/'.$target.'/info/');
        $getinfo = json_decode($getinfo[1]);
        $limit_unfollow = ($limit_unfollow=='maksimal') ? $getinfo->user->following_count-1 : $limit_unfollow-1;
        $curl_ig = 0;
        $listids = array();
        $unuser = array();
        do{
            $parameters = ($curl_ig>0) ? '?max_id='.$curl_ig : '';
            $req = proccess(1, $listaccounts['useragent'], 'friendships/'.$target.'/'.$tipe.'/'.$parameters, $listaccounts['cookies']);
            $req = json_decode($req[1]);
            for($i=0;$i<count($req->users);$i++):
                if(count($listids)<=$limit_unfollow)
                    $listids[count($listids)] = $req->users[$i]->pk;
            endfor;
            $curl_ig = (isset($req->next_max_id)) ? $req->next_max_id : 0;
        }while(count($listids)<=$limit_unfollow);
        for($i=0;$i<count($listids);$i++):
            if(!in_array($listids[$i], $whitelist)):
                    $curl_igross = proccess(1, $listaccounts['useragent'], 'friendships/create/'.$listids[$i].'/', $listaccounts['cookies'], hook('{"user_id":"'.$listids[$i].'"}'));
                    sleep($with_delay);
                    ;
                    flush();
            endif;
        endfor;
// End 
 
    ?>