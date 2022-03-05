<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2018
 */

return [
    '^shop\/(.*?)\/(.*?)\/(.*?)' => 'index/index/shop/pid/$1/aff/$2/tz/$3',
    // '^shop\/(.*?)\/(.*?)' => 'index/index/shop/pid/$1/aff/$2',
    '^qr\/(.*?)$' => 'service/Qr/get/text/$1',
    '^enQrcode.*?$' => 'index/index/enQrcode',
    '^geet.*?$' => 'index/index/geet',
    '^cx.html?$' => 'index/after/cx',
    '^refund.html$' => 'index/after/refund',
    '^user.html$' => 'user/index/login',
    '^jobSms(.*?)$' => 'index/job/sms',
    '^([0-9][1-9]){1,10}$' => 'index/index/index',
    
    
    // api模块
    '^check\/version\/(.*?)' => 'api/check/auth/version/$1',
    '^check\?version\=(.*?)' => 'api/check/auth/version/$1',
    '^download.*?$' => 'api/check/download',
    '^download\/version\/(.*?)' => 'api/check/download/version/$1',
    '^download\?version\=(.*?)' => 'api/check/download/version/$1',
    '^op\/(.*?)' => 'api/check/option/version/$1',
];