<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2020
 */

use ay\lib\Db;
use ay\lib\Cache;
use ay\lib\Curl;
use ay\drive\Update;

function user_password_auth($tj, $sql)
{
    $pass = user_password($tj);
    if ($pass == $sql) {
        return true;
    } else {
        return false;
    }
}

function user_password($password)
{
    return md5(sha1($password . 'AYPHP'));
}

function create_invite_code()
{
    $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $rand = $code[rand(0, 25)]
        . strtoupper(dechex(date('m')))
        . date('d')
        . substr(time(), -5)
        . substr(microtime(), 2, 5)
        . sprintf('%02d', rand(0, 99));
    for (
        $a = md5($rand, true),
        $s = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        $d = '',
        $f = 0;
        $f < 6;
        $g = ord($a[$f]),
        $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
        $f++
    ) ;
    return $d;
}

function randcount($n,$n2){
	$beilv=1;
	while(true){
	if($n*$beilv>=1){
		$beilv=$beilv;
		break;
	}else{
		$beilv=$beilv*10;
	}
	
	}
 
	$k=rand($n*$beilv,$n2*$beilv);
	$result=$k/$beilv;
	return $result;
	
}

function getSign($data, $key)
{
    ksort($data);
    $str = '';
    foreach ($data as $k => $v) {
        $str .= $k . '=' . $v . '&';
    }
    return md5($str . 'key=' . $key);
}

function smsbao($conf, $tel, $eid, $content = '')
{
    //----------------短信宝---------------------
    $statusStr = array(
        "0" => "短信发送成功",
        "-1" => "参数不全",
        "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
        "30" => "密码错误",
        "40" => "账号不存在",
        "41" => "余额不足",
        "42" => "帐户已过期",
        "43" => "IP地址限制",
        "50" => "内容含有敏感词"
    );
    if (is_null($tel)) {
        return ['status' => 0, 'msg' => "error:手机号为空"];
    }
    $tel = str_replace(' ', '', $tel);
    if (empty($content)) $content = "【" . $conf['sms_sign'] . "】您好！您的订单，已由（百世快递）发货，快递订单编号为{$eid}，提交订单24小时后可查询单号！";
    $sendurl = "http://api.smsbao.com/sms?u=" . $conf['sms_ak'] . "&p=" . md5($conf['sms_sk']) . "&m=" . $tel . "&c=" . urlencode($content);
    $sendurl = str_replace(" ", "", $sendurl);
    $result = Curl::url($sendurl)->get();
    if ($result == '0') {
        return ['status' => 1, 'msg' => $content];
    } else {
        return ['status' => 0, 'msg' => "error:" . $statusStr[$result]];
    }

}


function dwz_11($siteConf, $url, $pid, $uid)
{
    $url = 'http://' . $url . '/shop/' . $pid . '/' . $uid . '/1';
    $sUrl = 'http://mrw.so/api.php?format=json&key=' . $siteConf['dwz_token'] . '&url=' . urlencode($url) . '&expireDate=2030-03-31';
    $res = file_get_contents($sUrl);
    $res = json_decode($res, true);
    if ($res['url'] != "") {
        return $res['url'];
    } else {
        return $url;
    }

}

// 
function dwz($siteConf, $url, $pid, $uid)
{
    $url = 'http://' . $url . '/shop/' . $pid . '/' . $uid . '/1';
    // $row = Db::name('dwz')->field('dwz')->where('url', $url)->find();
    if (false) {
        return $row['dwz'];
    } else {
        $sUrl = 'http://check.uomg.com/api/dwz/vxrand?token=' . $siteConf['dwz_token'] . '&longurl=' . ($url);
        // return $sUrl;
        $res = file_get_contents($sUrl);
        $res = json_decode($res, true);
        if ($res['ae_url'] != "") {
            $insert = [
                'type' => 1,
                'url' => $url,
                'dwz' => $res['ae_url']
            ];
            // Db::name('dwz')->insert($insert);
            return $res['ae_url'];
        } else {
            return $url;
        }

    }

}

function UNHTML($content)
{
    $content = htmlspecialchars($content);
    $content = str_replace(chr(13), "<br>", $content);
    $content = str_replace(chr(32), " ", $content);
    $content = str_replace("[_[", "<", $content);
    $content = str_replace("]_]", ">", $content);
    $content = str_replace("|_|", " ", $content);
    return trim($content);
}

function wxDomain($siteConf, $url)
{
    $url = "http://api.snsyrp.cn/api/vx/urlsec/?token={$siteConf['dwz_token']}&domain=" . $url;
    $res = file_get_contents($url);
    return json_decode($res, true);
}

function getDay($num)
{
    $time = time();//时间
    $now = date("w", $time);//获取今天的周几
    $now = $now == 0 ? 7 : $now;//修正周日
    return date("Ymd", $time - ($now - $num) * 86400);//得到周二
}

function up()
{
    if (SQ == 1) return ['code' => 200, 'msg' => $res, 'data' => []];
    $url = "http://shop.vclove.cn/check?version=" . PRODUCTVEWRSION;
    $res = Update::instance()->start($url, PRODUCTVEWRSION);
    // var_dump($res);exit;
    if ($res and !is_array($res) and $res['code'] != 400) {
        return ['code' => 200, 'msg' => $res, 'data' => []];
    } else if ($res and is_array($res) and $res['code'] != 400) {
        return ['code' => 201, 'msg' => '请更新', 'data' => $res['data']];
    } else {

        return ['code' => 400, 'msg' => $res['msg'], 'data' => []];
    }
}

function confUp($url)
{
    return Update::instance()->autoUpdate($url);
}

function objectToArray($e)
{
    $e = (array)$e;
    foreach ($e as $ke => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $e[$ke] = (array)objectToArray($v);
        }
    }
    return $e;
}

function setkey($sq = 0)
{

    define('SQ', $sq);
    if ($sq == 0) return;
    $name = 'shop';
    $time = time();
    $path = PUB . '/key.txt';
    $ip = gethostbyname(gethostname());

    file_put_contents(PUB . '/ip.txt', $ip);


    if (!is_file($path)) {
        fail("请联系管理员授权");
        exit;
    }
    $key = file_get_contents($path);
    $res = authcode($key, 'DECODE', 'adsdfsdfdhbfgvgbdfsasdcfsa');
    $res = json_decode($res, true);
    if ($res['name'] != $name) {
        fail("授权码错误");
        exit;
    } else if ($res['ip'] != $ip) {
        fail("授权IP");
        exit;
    }
    if ($res['time'] < $time) {
        fail("授权已到期");
        exit;
    }

}

function getRand($len, $chars = null)
{
    if (is_null($chars)) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    }
    mt_srand(10000000 * (double)microtime());
    for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}

function getRand2($len, $chars = null)
{
    if (is_null($chars)) {
        $chars = "0123456789";
    }
    mt_srand(10000000 * (double)microtime());
    for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}

function pan($url, $pid, $uid)
{
    $rand1 = getRand(3);
    $rand2 = getRand2(2);
    $res = Cache::instance()->get('dwz');
    if (!$res) $res = [];
    while (true) {
        if (!array_key_exists($rand1 . '_' . $rand2, $res)) {
            break;
        }
        $rand1 = getRand(5);
        $rand2 = getRand2(2);
    }
    if (substr($url, 0, stripos($url, '.')) == "*") {
        $url = substr($url, stripos($url, '.') + 1, strlen($url));
    }
    $res[$rand1 . '_' . $rand2] = ['aff' => $uid, 'pid' => $pid];
    Cache::instance()->set('dwz', $res);
    return "http://" . $rand1 . "." . $url . '/' . $rand2;
}

setkey();
