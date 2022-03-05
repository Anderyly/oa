<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc
 * @copyright Copyright (c) 2021
 */

namespace app\api\controller;

use ay\lib\Db;
use Exception;
use ay\lib\Str;
use ay\lib\Json;

class Common
{

    public int $uid;
    public string $token;
    public string $api;
    public array $data;
    public int $expiry;
    public string $root;

    public array $rule = [
        '/api/user/login','/api/project/odata','/api/logo/get','/api/contract/odata',
//        '/api/user/getmenu'
    ];
    public array|bool $user;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->expiry = time() + 60 * 60 * 6;
        $this->api = strtolower('/' . MODE . '/' . CONTROLLER . '/' . ACTION);
        $this->data = R('post.');
        if (!in_array($this->api, $this->rule)) {
            $this->authToken($this->data['token'] ?? Json::msg(1001, 'token不存在'));
        }


        // echo $this->uid;
    }

    /**
     * @throws Exception
     */
    public function authToken($token)
    {
        $res = Db::name('token')->field('uid,expiry')->order('id desc')->where('token', $token)->find();
        if (!$res) Json::msg(1002, '请登入');
        if ($res['expiry'] < time()) Json::msg(1003, '请重新登入');

        Db::name('token')->where('token', $token)->update(['expiry' => $this->expiry]);
        $this->token = $this->data['token'] ?? "";

        $user = Db::name("user")->where("uid", $res['uid'])->find();
        if (!$user) Json::msg(1003, '请重新登入');
        $this->uid = $user['uid'];
        $this->root = $user['root'];
        $this->user = $user;
        
        unset($this->data['token']);
        unset($this->data['s']);
        $this->menuRole();
    }

    public function menuRole()
    {
        if ($this->root == "*") return;
        $r = Db::name("menu")->field('id')->where('url', strtolower(CONTROLLER . '/' . ACTION))->find();
        if ($r) {
            $root = explode(',', $this->root);
            if (!in_array($r['id'], $root)) Json::msg(1020, '没有权限访问！');

            if (in_array($r['id'], [51, 55]) and $this->data['type'] == 1) {
                Json::msg(1020, '您没有权限访问!');
            }
        }

    }

    /**
     * 生成token
     * @param $uid
     * @return string
     * @throws Exception
     */
    public function makeToken($uid): string
    {
        $res = Db::name('token')->field('id,token,expiry')->order('id desc')->where('uid', $uid)->find();
        if ($res['expiry'] > time()) {
            Db::name('token')->where('id', $res['id'])->update(['expiry' => $this->expiry]);
            return $res['token'];
        }
        $uuid = Str::instance()->guid();
        $token = md5($uuid . time());
        $arr = [
            'uid' => $uid,
            'token' => $token,
            'expiry' => $this->expiry,
            'created_at' => time()
        ];
        Db::name('token')->insert($arr);
        return $token;
    }

    public function rJson($res, $data = [])
    {
        if ($res) {
            Json::msg(200, '操作成功', $data);
        } else {
            Json::msg(400, '操作失败', $data);
        }
    }
}