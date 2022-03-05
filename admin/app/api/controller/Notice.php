<?php
/**
 * 公告
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2021
 */

namespace app\api\controller;

use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Validator;
use Exception;

class Notice extends Common
{

    private string $table = "oa_notice";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;
        if (isset($data['t']) and !empty($data['t']) and $data['t'] != "all") $where[] = ["l.uid", '=', $data['t']];
        $where[] = ["n." . $this->field, '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['n.title', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("n")
            ->field('n.*,user.nickname')
            ->join("oa_user user", "n.uid=user.uid", "LEFT")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("n.id desc")
            ->select();
        $count = Db::table($this->table)
            ->alias("n")
            ->field($this->field)
            ->where($where)
            ->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    public function add()
    {
        $data = $this->data;


        $checkArr = [
            'title' => 'require',
            'content' => 'require',
            'uid' => 'require',
//            'content' => 'require',
        ];
        Validator::check($data, $checkArr);

        if (isset($data['department']) and !empty($data['department'])) {
            $department = explode(',', $data['department']);
            $str = '';

            foreach ($department as $v) {
                $user = Db::name("user")->field('uid')->where('unit', $v)->select();
                foreach ($user as $v1) {
                    $str .= $v1['uid'] . ',';
                }
            }
            $data['tid'] = rtrim($str, ',');
        }

        $data['created_at'] = time();

        $data['content'] = $_POST['content'];

        $res = Db::table($this->table)->insert($data);

        $this->rJson($res);

    }

    public function del()
    {
        $id = $this->data['id'];
        $idArr = explode(',', $id);
        $a = 1;
        foreach ($idArr as $v) {
            $row = Db::table($this->table)->where($this->field, $v)->delete();
            if (!$row) {
                $a = 0;
                break;
            }
        }

        $this->rJson($a);
    }

    public function detail()
    {
        $id = $this->data['id'];
        $res = Db::table($this->table)
            ->field("l.*,user.nickname")
            ->alias("l")
            ->join("oa_user user", "l.uid=user.uid", "left")
            ->where("l.id", $id)
            ->find();
        if (!$res) {
            Json::msg(400, '数据不存在');
        } else {

            $read = explode(',', $res['read']);

            if (!in_array($this->uid, $read)) {
                $read[] = $this->uid;
                $str = implode(",", $read);
                Db::table($this->table)->where('id', $res['id'])->update(['read' => $str]);
            }

            $jsrArr = explode(',', $res['tid']);

            $jsr = '';
            foreach ($jsrArr as $v) {
                $user = Db::table("oa_user")
                    ->field("nickname")
                    ->where("uid", $v)
                    ->find();
                if ($user) $jsr .= $user['nickname'] . ',';
            }

            $res['jsr'] = rtrim($jsr, ',');

            $res['created_at'] = date("Y-m-d H:i:s", $res['created_at']);
            Json::msg(200, 'success', $res);
        }
    }

}