<?php
/**
 * 用户
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2021
 */

namespace app\api\controller;

use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Upload;
use ay\lib\Validator;
use Exception;

class User extends Common
{

    public function upload()
    {
        $data = $this->data;
        $upload = new Upload(PUB . '/upload/', ['jpg', 'jpeg', 'gif', 'png', 'execl', 'zip', 'rar', 'doc', 'docx', 'pdf', 'mp4', 'avi', 'xls', 'xlsx']);
        $res = $upload->operate('file');
        if (isset($res['error'])) {
            Json::msg(400, $res['error']);
        }
        $insert = [
            'uid' => $this->uid,
            'url' => URL . '/upload/' . $res['basename'],
            'ext' => $res['ext'],
            'name' => $res['name'],
            'file_type' => $res['fieldName'],
            'size' => ceil($res['size'] / 1024),
            'created_at' => date('Y-m-d H:i:s'),
            'type' => $data['ctype'],
            'cid' => $data['cid']

        ];
        $r = Db::name("upload")->insert($insert);
        $arr = [
            'id' => $r,
            'url' => URL . '/upload/' . $res['basename'],
            'ext' => $res['ext'],
            'name' => $res['name'],
            'file_type' => $res['fieldName'],
            'size' => ceil($res['size'] / 1024),
        ];

        Json::msg(200, 'success', $arr);
    }

    /**
     * @throws Exception
     */
    public function login()
    {
        $data = $this->data;
        $checkArr = [
            'account' => 'require|empty',
            'password' => 'require|empty',
        ];

        Validator::check($data, $checkArr);

        $res = Db::name('user')
            ->where("account", $data['account'])
            ->find();
        if (!$res) Json::msg(400, '账号不存在');
        if (!user_password_auth($data['password'], $res['password'])) Json::msg(400, '密码错误');

        if ($res['status'] != 1) Json::msg(400, '用户已离职');

        $res['token'] = $this->makeToken($res['uid']);
        unset($res['password']);
        unset($res['status']);
//        unset($res['uid']);

        Json::msg(200, "登入成功", $res);

    }

    public function logout()
    {
        $token = $this->token;

        $res = Db::name("token")->where("token", $token)->delete();

        $this->rJson($res);

    }

    /**
     * 获取用户信息
     */
    public function detail()
    {
        $data = $this->data;
        $checkArr = [
            'id' => 'require|empty',
        ];

        Validator::check($data, $checkArr);
        $res = Db::name('user')
            ->alias("user")
            ->field("user.root,user.uid,user.account,user.nickname,user.address,user.phone,user.gender,user.email,user.qq,user.meta,user.status,c.name as unit_name,c.id as unit_id")
            ->join("oa_department c", "user.unit=c.id", "left")
            ->where("user.uid", $data['id'])
            ->find();

        if (!$res) {
            Json::msg(400, '账号不存在');
        }
        Json::msg(200, "success", $res);
    }

    /**
     * @throws Exception
     */
    public function edit()
    {
        $data = $this->data;

        $checkArr = ['qq', 'phone', 'address', 'password', 'rePassword', 'gender', 'email', 'meta', 'root', 'unit', 'nickname', 'uid', 'status'];

        foreach ($data as $k => $v) {
            if (!in_array($k, $checkArr)) {
                Json::msg(400, '参数错误');
            }
        }

        if (isset($data['password']) and isset($data['rePassword'])) {
            if ($data['password'] == $data['rePassword']) {
                $data['password'] = user_password($data['password']);
                unset($data['rePassword']);
            } else {
                Json::msg(400, '两次密码不一致');
            }
        }
        $uid = $data['uid'];

        unset($data['uid']);

        $res = Db::name('user')->where('uid', $uid)->update($data);
        $this->rJson($res);
    }

    /**
     * @throws Exception
     */
    public function del()
    {
        $id = $this->data['id'];
        $idArr = explode(',', $id);
        $a = 1;
        foreach ($idArr as $v) {
            $row = Db::name('user')->where('uid', $v)->delete();
            if (!$row) {
                $a = 0;
                break;
            }
        }

        $this->rJson($a);
    }

    /***
     * @throws Exception
     */
    public function add()
    {
        $data = $this->data;
        $checkArr = [
            'account' => 'require|empty',
            'password' => 'require|empty',
            'rePassword' => 'require|empty',
//            'unit' => 'require',
//            'nickname' => 'require',
//            'gender' => 'require',
//            'phone' => 'require',
//            'email' => 'require',
//            'address' => 'require',
//            'meta' => 'require',
            'root' => 'require',
        ];
        Validator::check($data, $checkArr);

        if ($data['password'] != $data['rePassword']) {
            Json::msg(400, '两次密码不一致');
        }

        $row = Db::name('user')->field('uid')->where('account', $data['account'])->find();
        if ($row) {
            Json::msg(400, '当前账号已存在');
        }

        $data['password'] = user_password($data['password']);
        unset($data['rePassword']);

        $data['created_at'] = time();
        $data['status'] = 1;

        $res = Db::name('user')->insert($data);
        $this->rJson($res);

    }

    public function getAllRoot()
    {
        $res = Db::name("menu")
            ->field('id,pid,title')
            ->order('id asc')
            ->order('sort asc')
            ->select();
        $tree = tree($res);


        Json::msg(200, 'success', $tree);

    }

    public function getRoot()
    {
        $res = Db::name("user")->field('root')->where('uid', $this->uid)->find();
        $arr = [];
        $root = explode(',', $res['root']);

        foreach ($root as $v) {
            $arr[] = Db::name("menu")->field('id,pid,title')->where('id', $v)->find();
        }
//        dump($arr);
        $tree = tree($arr);

        Json::msg(200, 'success', $tree);
    }

    public function getAll()
    {
        $res = Db::name("user")
            ->field("uid as id, nickname as text")
            ->where('status', 1)
            ->select();
//        echo Db::name("s")->getLastSql();

        $res[] = ['text' => '不分配', 'id' => 0];

        Json::msg(200, 'success', $res);
    }

    public function getAll1()
    {
        $res = Db::name("user")
            ->field("uid as value, nickname as title")
            ->where('status', 1)
            ->select();

        Json::msg(200, 'success', $res);
    }

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;
        $where[] = ["user.uid", '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['user.account', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::name("user")
            ->alias("user")
            ->field('user.uid,user.account,user.nickname,user.address,user.phone,user.gender,user.email,user.qq,c.name as unit_name')
            ->join("oa_department c", "user.unit=c.id", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("user.uid desc")
            ->select();
        $count = Db::name("user")->alias("user")->field("uid")->where($where)->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);
    }

    // TODO 用户登入判断权限判断 消息公告 报销审核 项目审核 考勤审核 工具审核
    public function corn()
    {

    }

    public function getMenu()
    {
        $user = Db::name("user")->field('root')->where('uid', $this->uid)->find();

        $root = explode(',', $user['root']);

        $arr = [];
        $arr[] = [
            "title" => "主题",
            "pageURL" => "theme",
            "name" => "主题",
            "icon" => "theme",
            "openType" => 1,
            "maxOpen" => -1,
            "extend" => false,
            "childs" => null,
            "id" => 100000
        ];
        foreach ($root as $v) {
            $c = Db::name("menu")->field('id,pid,icon,title,page')
                ->where('id', $v)
                ->where('pid', 0)
                ->find();
            if (!$c) continue;

            $arr[] = [
                "title" => $c['title'],
                "pageURL" => $c['page'],
                "name" => $c['title'],
                "icon" => $c['icon'],
                "openType" => 2,
//                "maxOpen" => -1,
                "extend" => false,
                "childs" => null,
                "id" => $c['id']
            ];
        }

        exit(json_encode([
            'code' => 1,
            'message' => 'success',
            'data' => $arr
        ]));
    }

    public function getMenu1()
    {
        $user = Db::name("user")->field('root')->where('uid', $this->uid)->find();

        $root = explode(',', $user['root']);

        $arr = [];
        $arr[] = [
            "title" => "主题",
            "pageURL" => "theme",
            "name" => "主题",
            "icon" => "theme",
            "openType" => 1,
            "maxOpen" => -1,
            "extend" => false,
            "childs" => null,
            "id" => 100000
        ];
        foreach ($root as $v) {
            if (count($arr) >= 10) break;
            $c = Db::name("menu")->field('id,pid,icon,title,page')
                ->where('id', $v)
                ->where('pid', 0)
                ->find();
            if (!$c) continue;

            $arr[] = [
                "title" => $c['title'],
                "pageURL" => $c['page'],
                "name" => $c['title'],
                "icon" => $c['icon'],
                "openType" => 2,
//                "maxOpen" => -1,
                "extend" => false,
                "childs" => null,
                "id" => $c['id']
            ];

        }

        exit(json_encode([
            'code' => 1,
            'message' => 'success',
            'data' => $arr
        ]));
    }

    public function msg()
    {
        $root = explode(',', $this->root);

        $noticeSql = "SELECT count(id) as id FROM `oa_notice` WHERE FIND_IN_SET($this->uid, tid) AND !FIND_IN_SET($this->uid, readme)";

        $notice = Db::name("s")->doSql($noticeSql);
        $noticeNum = $notice[0]['id'];
        $arr = [];
        if ($noticeNum > 0) {
            $arr[] = [
                'msg' => "您有新的公告未查看",
                'time' => date('Y-m-d H:i:s')
            ];
        }

        // 报告填写
        $report_tx = Db::name("report")->field('id')->where('report_uid', $this->uid)->count();
        if ($report_tx > 0) {
            $arr[] = [
                'msg' => "您有新的报告需要填写",
                'time' => date('Y-m-d H:i:s')
            ];
        }

        // 报告审核
        $report_verify = Db::name("report")->field('id')->where('status', 2)->count();
        if ($report_verify > 0 and in_array(83, $root)) {
            $arr[] = [
                'msg' => "您有新的报告需要审核",
                'time' => date('Y-m-d H:i:s')
            ];
        }
        // 报告盖章
        $report_verify = Db::name("report")->field('id')->where('status', 3)->count();
        if ($report_verify > 0 and in_array(84, $root)) {
            $arr[] = [
                'msg' => "您有新的报告需要盖章",
                'time' => date('Y-m-d H:i:s')
            ];
        }

        // 报告结案
        $report_verify = Db::name("report")->field('id')->where('status', 4)->count();
        if ($report_verify > 0 and in_array(85, $root)) {
            $arr[] = [
                'msg' => "您有新的报告需要结案",
                'time' => date('Y-m-d H:i:s')
            ];
        }

        /**
         * SetTip("white", "您有新的报销单需要批准!",
        SetTip("white", "您有新的报销单需要审核!
         */
        Json::msg(200, 'success', $arr);
    }

}