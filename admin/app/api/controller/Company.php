<?php
/**
 * 公司
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

class Company extends Common
{

    private string $table = "oa_company";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;
        $where[] = [$this->field, '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->field('id,name,address,phone')
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("id desc")
            ->select();
        $count = Db::table($this->table)->field($this->field)->where($where)->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    public function add()
    {
        $data = $this->data;

        $checkArr = [
            'address' => 'require',
            'name' => 'require',
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
        $res = Db::table($this->table)->where("id", $id)->find();
        if (!$res) {
            Json::msg(400, '公司不存在');
        } else {
            Json::msg(200, 'success', $res);
        }
    }

    public function edit()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'require',
            'address' => 'require',
            'name' => 'require',
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

}