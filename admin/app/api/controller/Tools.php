<?php
/**
 * 工具
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

class Tools extends Common
{

    private string $table = "oa_tools";
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
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("d")
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
            'name' => 'require',
            'meta' => 'require',
        ];
        Validator::check($data, $checkArr);

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
            ->where("id", $id)
            ->find();
        if (!$res) {
            Json::msg(400, '数据不存在');
        } else {
            Json::msg(200, 'success', $res);
        }
    }

    public function edit()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'require',
            'name' => 'require',
            'meta' => 'require',
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
            ->field("id as value,name as title")
            ->where("status", 0)
            ->select();

        Json::msg(200, 'success', $res);
    }

    public function getAll1()
    {
        $res = Db::table($this->table)
            ->field("id as value,name as title")
            ->select();

        Json::msg(200, 'success', $res);
    }

}