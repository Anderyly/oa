<?php
/**
 * 客户
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

class Customer extends Common
{

    private string $table = "oa_customer";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;

        $where[] = ['uid', '=', $this->uid];
        $where[] = ['report_id', '=', $data['report_id']];

        $row = Db::table($this->table)
            ->where($where)
            ->order("id desc")
            ->select();
        $count = Db::table($this->table)
            ->field($this->field)
            ->where($where)
            ->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    public function add()
    {
        $data = $this->data;


        $checkArr = [
            'company_name' => 'require',
            'user_name' => 'require',
            'report_id' => 'require',
        ];
        Validator::check($data, $checkArr);

        $data['created_at'] = time();

        $data['uid'] = $this->uid;

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