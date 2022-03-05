<?php
/**
 * 报告类型管理
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

class ReportType extends Common
{

    private string $table = "oa_report_type";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['company_id']) and !empty($data['company_id'])) $where[] = ['n.company_id', '=', $data['company_id']];

        $where[] = ["n." . $this->field, '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['n.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("n")
            ->field('n.*,b.name as company_name')
            ->join("oa_company b", "n.company_id=b.id", 'left')
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
            'company_id' => 'require',
            'text' => 'require',
            'type' => 'require',
        ];
        Validator::check($data, $checkArr);

        $data['uid'] = $this->uid;
        $data['created_at'] = date("Y-m-d H:i:s", time());

        $res = Db::table($this->table)->insert($data);

        $this->rJson($res);

    }

    public function edit()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'require',
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
            ->field("a.*,b.name as company_name")
            ->alias('a')
            ->join("oa_company b", "a.company_id=b.id", 'left')
            ->where("a.id", $id)
            ->find();
        if (!$res) {
            Json::msg(400, '数据不存在');
        }
        Json::msg(200, 'success', $res);

    }

    public function getAll()
    {
        $res = Db::table($this->table)
            ->field("id, name as text")
            ->select();

        Json::msg(200, 'success', $res);
    }

    public function text()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required'
        ];
        Validator::check($data, $checkArr);

        $res = Db::table($this->table)->where('id', $data['id'])->find();
        $text = $res['text'];

        $text = str_replace("{0}", date('Y', time()), $text);
        $text = str_replace("{1}", $res['number'], $text);

        Json::msg(200, 'success', ['text' => $text]);

    }


}