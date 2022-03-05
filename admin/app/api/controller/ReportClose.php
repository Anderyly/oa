<?php
/**
 * 结案 - 报告
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

class ReportClose extends Common
{

    private string $table = "oa_report";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['report_type']) and !empty($data['report_type'])) $where[] = ["l.report_type", '=', $data['report_type']];
        if (isset($data['charge']) and $data['charge'] != "") $where[] = ["l.charge", '=', $data['charge']];

        $where[] = ["l.fgs", '=', 0];
        $where[] = ["l.status", '=', 5];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['l.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("l")
            ->field("l.id,l.name,l.report_text,l.created_at,l.evaluation_aim,l.bank,l.status,l.report_uid,t.name as report_type_name,u.nickname,l.charge")
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

    public function branchList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['report_type']) and !empty($data['report_type'])) $where[] = ["l.report_type", '=', $data['report_type']];
        if (isset($data['charge']) and $data['charge'] != "") $where[] = ["l.charge", '=', $data['charge']];

        $where[] = ["l.fgs", '=', 1];
        $where[] = ["l.status", '=', 5];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['l.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("l")
            ->field("l.id,l.name,l.report_text,l.created_at,l.evaluation_aim,l.bank,l.status,l.report_uid,t.name as report_type_name,u.nickname,l.charge")
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

    /**
     * 打回作报告
     * @return void
     * @throws Exception
     */
    public function repulse()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::table($this->table)->where('id', $data['id'])->find();
        if (!$res) Json::msg(400, '数据不存在');
        if ($res['status'] != 5) Json::msg(400, '当前不是结案状态');

        $asd = [
            'status' => 1,
        ];
        Db::table($this->table)->where('id', $data['id'])->update($asd);

        $this->rJson($res);
    }

    /**
     * 归档
     * @return void
     * @throws Exception
     */
    public function end()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::table($this->table)->where('id', $data['id'])->find();
        if (!$res) Json::msg(400, '数据不存在');
        if ($res['status'] != 5) Json::msg(400, '当前不是结案状态');

        $asd = [
            'status' => 6,
        ];
        Db::table($this->table)->where('id', $data['id'])->update($asd);

        $this->rJson($res);
    }

}