<?php
/**
 * 报销
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

class Reimbursement extends Common
{

    private string $table = "oa_reimbursement";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if ((!empty($data['status']) or $data['status'] == 0)) {
            $where[] = ["d.status", '=', $data['status']];
        }

        if (isset($data['type']) and !empty($data['type'])) {
            $where[] = ["d.type", '=', $data['type']];
        }

        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ["d.amount", ' like ', $data['key'] . '%'];
        }

        $where[] = ["d." . $this->field, '!=', 0];

        $row = Db::table($this->table)
            ->alias("d")
            ->field("d.*,u.nickname")
            ->join("oa_user u", "d.uid=u.uid", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();

        $count = Db::table($this->table)
            ->alias("d")
            ->field($this->field)
            ->where($where)
            ->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    public function myList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        $where[] = ["d.uid", '=', $this->uid];

        if ((!empty($data['status']) or $data['status'] == 0)) {
            $where[] = ["d.status", '=', $data['status']];
        }

        if (isset($data['type']) and !empty($data['type'])) {
            $where[] = ["d.type", '=', $data['type']];
        }

        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ["d.amount", ' like ', $data['key'] . '%'];
        }


        $row = Db::table($this->table)
            ->alias("d")
            ->field("d.*,u.nickname")
            ->join("oa_user u", "d.uid=u.uid", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();

        $count = Db::table($this->table)
            ->alias("d")
            ->field($this->field)
            ->where($where)
            ->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    public function verify()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'require',
            'status' => 'require',
        ];
        Validator::check($data, $checkArr);

        $res = Db::table($this->table)->where('id', $data['id'])->find();
        if (!$res) Json::msg(400, '数据不存在');

        if ($res['status'] == 5) Json::msg(400, '报销已结束，不可更改');

        if ($res['status'] == $data['status']) Json::msg(400, '不可重复审核或批准');

        $arr = ['status' => $data['status']];

        // 审核
        if ($data['status'] == 1 or $data['status'] == 3) {
            $arr['verify_uid'] = $this->uid;
            $arr['verify_at'] = time();
            $arr['verify_meta'] = $data['verify_meta'];
        }

        // 批准
        if ($data['status'] == 2 or $data['status'] == 4 or $data['status'] == 5) {
            $arr['approval_uid'] = $this->uid;
            $arr['approval_at'] = time();
            $arr['approval_meta'] = $data['approval_meta'];

        }

        $row = Db::table($this->table)->where('id', $data['id'])->update($arr);


        $this->rJson($row);


    }

    public function add()
    {
        $data = $this->data;

        $checkArr = [
            'type' => 'require',
            'amount' => 'require',
            'actual_amount' => 'require',
            'invoice_balance' => 'require',
            'bill' => 'require',
        ];
        Validator::check($data, $checkArr);

        $data['uid'] = $this->uid;
        $data['created_at'] = time();

        $res = Db::table($this->table)->insert($data);

        $this->rJson($res);

    }

    public function edit()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'require',
            'type' => 'require',
            'amount' => 'require',
            'actual_amount' => 'require',
            'invoice_balance' => 'require',
            'bill' => 'require',
            'content' => 'require',
        ];
        Validator::check($data, $checkArr);

        $id = $data['id'];
        unset($data['id']);

        $res = Db::table($this->table)->where('id', $id)->update($data);

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
            ->alias('d')
            ->field('d.*,u.nickname')
            ->join("oa_user u", "d.uid=u.uid", "left")
            ->where("d.id", $id)
            ->find();

        $user = Db::name("user")->field('nickname')->where('uid', $res['verify_uid'])->find();
        $res['verify_nickname'] = $user['nickname'] ?? "";
        $res['verify_at'] = date("Y-m-d H:i:s", $res['verify_at']);
        $res['created_at'] = date("Y-m-d H:i:s", $res['created_at']);
        $user = Db::name("user")->field('nickname')->where('uid', $res['approval_uid'])->find();
        $res['approval_nickname'] = $user['nickname'] ?? "";
        $res['approval_at'] = date("Y-m-d H:i:s", $res['approval_at']);


        if (!$res) {
            Json::msg(400, '数据不存在');
        } else {
            Json::msg(200, 'success', $res);
        }
    }

}