<?php
/**
 * 合同
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

class Contract extends Common
{

    private string $table = "oa_contract";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['t'])) $where[] = ["d.type", '=', $data['t']];
        if (isset($data['uid'])) $where[] = ["d.uid", '=', $data['uid']];


        $where[] = ["d." . $this->field, '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.title', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("d")
            ->field('d.*,user.nickname,s.base')
            ->join("oa_user user", "d.uid=user.uid", "LEFT")
            ->join("oa_contract_type s", "d.type_=s.id", "LEFT")
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

        if (isset($data['t'])) $where[] = ["d.type", '=', $data['t']];
        $where[] = ["d.uid", '=', $this->uid];


        $where[] = ["d." . $this->field, '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.title', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("d")
            ->field('d.*,user.nickname,s.base')
            ->join("oa_user user", "d.uid=user.uid", "LEFT")
            ->join("oa_contract_type s", "d.type_=s.id", "LEFT")
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

    public function add()
    {
        $data = $this->data;

        $checkArr = [
            'title' => 'require',
            'content' => 'require',
            'type' => 'require',
        ];
        Validator::check($data, $checkArr);

        $data['created_at'] = time();

        $data['content'] = $_POST['content'];
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
            ->field("l.*,user.nickname,s.base")
            ->alias("l")
            ->join("oa_user user", "l.uid=user.uid", "left")
            ->join("oa_contract_type s", "l.type_=s.id", "LEFT")
            ->where("l.id", $id)
            ->find();

        if (!empty($res['content'])) {
            $ziliaoArr = explode(',', $res['content']);
            foreach ($ziliaoArr as $v1) {
                $res['contentData'][] = Db::name("upload")
                    ->field('id,name,ext,url,size')
                    ->where('id', $v1)
                    ->find();
            }
        }

        if (!$res) {
            Json::msg(400, '数据不存在');
        } else {
            $res['created_at'] = date("Y-m-d H:i:s", $res['created_at']);
            Json::msg(200, 'success', $res);
        }
    }

    public function edit()
    {
        $data = $this->data;

        $id = $data['id'];
        unset($data['id']);

        if (isset($data['operate_uid'])) {
            $arr = [
                'operate_uid' => $data['operate_uid'],
                'meta' => $data['meta'],
                'operate_at' => time(),
                'status' => $data['status']
            ];
        } else {
            if (strlen($data['start_time'])) $data['start_time'] = strtotime($data['start_time']);
            if (strlen($data['end_time'])) $data['end_time'] = strtotime($data['end_time']);
            $arr = $data;
        }

        $res = Db::table($this->table)->where("id", $id)->update($arr);

        $this->rJson($res);

    }

    public function getAll()
    {
        $res = Db::table($this->table)
            ->field("id, name as text")
            ->select();

        Json::msg(200, 'success', $res);
    }

    public function odata()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::name("upload")
            ->field("id,name,ext,url,size")
            ->where('type', 3)
            ->where('cid', $data['id'])
            ->select();

        Json::msg(200, 'success', $res);
    }

}