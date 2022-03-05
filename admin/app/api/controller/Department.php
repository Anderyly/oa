<?php
/**
 * 部门
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

class Department extends Common
{

    private string $table = "oa_department";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;
        $where[] = ["bm." . $this->field, '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['bm.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("bm")
            ->field('bm.id,bm.name,bm.email,bm.qq,bm.phone,bm.meta,user.nickname,gs.name as gsName')
            ->join("oa_user user", "bm.uid=user.uid", "LEFT")
            ->join("oa_company gs", "bm.company_id=gs.id", "LEFT")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("bm.id desc")
            ->select();
        $count = Db::table($this->table)
            ->alias("bm")
            ->field($this->field)
            ->where($where)
            ->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    public function add()
    {
        $data = $this->data;

        $checkArr = [
            'qq' => 'require',
            'uid' => 'require',
            'company_id' => 'require',
            'name' => 'require',
            'meta' => 'require',
            'phone' => 'require',
        ];
        Validator::check($data, $checkArr);

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
            ->field("d.*,c.name as company_name,user.nickname")
            ->alias("d")
            ->join("oa_company c", "d.company_id=c.id", "left")
            ->join("oa_user user", "d.uid=user.uid", "left")
            ->where("d.id", $id)
            ->find();
        if (!$res) {
            Json::msg(400, '公司不存在');
        } else {
            if ($res['uid'] == 0) {
                $res['nickname'] = '不分配';
            }
            Json::msg(200, 'success', $res);
        }
    }

    public function edit()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'require',
            'qq' => 'require',
            'uid' => 'require',
            'company_id' => 'require',
            'name' => 'require',
            'meta' => 'require',
            'phone' => 'require',
        ];
        Validator::check($data, $checkArr);

        $id = $data['id'];
        unset($data['id']);

        $res = Db::table($this->table)->where("id", $id)->update($data);

        $this->rJson($res);

    }

    public function getAll()
    {
        $res = Db::table($this->table)
            ->field("id, name as text")
            ->select();

        Json::msg(200, 'success', $res);
    }

    public function getAll1()
    {
        $res = Db::table($this->table)
            ->field("id as value, name as title")
            ->select();

        Json::msg(200, 'success', $res);
    }

}