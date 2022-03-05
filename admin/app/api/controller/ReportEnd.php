<?php
/**
 * 归档 - 报告
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

class ReportEnd extends Common
{

    private string $table = "oa_report";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

//        $where[] = ["l.fgs", '=', 0];
        $where[] = ["l.status", '=', 6];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['l.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("l")
            ->field("l.id,l.name,l.report_text,l.created_at,l.evaluation_aim,l.bank,l.status,l.report_uid,t.name as report_type_name,u.nickname")
            ->join("oa_report_type t", "l.report_type=t.id")
            ->join("oa_user u", "l.uid=u.uid")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("l.id desc")
            ->select();

        foreach ($row as $k => $v) {
            $user = Db::table("oa_user")->field('nickname')->where('uid', $v['report_uid'])->find();
            $row[$k]['report_nickname'] = $user['nickname'] ?? "";
            unset($row[$k]['report_uid']);
        }

        $count = Db::table($this->table)
            ->alias("l")
            ->field($this->field)
            ->where($where)
            ->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    /**
     * 删除
     * @return void
     * @throws Exception
     */
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
}