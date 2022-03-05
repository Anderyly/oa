<?php
/**
 * 财务报告
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2021
 */


namespace app\api\controller;

use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Validator;

class FinanceReport extends Common
{

    public function getList()
    {

        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        $where[] = ['a.id', '!=', 0];

        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['b.report_text', 'LIKE', '%' . $data['key'] . '%'];
        }

        $res = Db::name("report_amount")
            ->alias('a')
            ->field("a.id,b.report_text,u.nickname as report_nickname,b.end_before_amount,b.really_amount,b.bil_amount,end_uid,end_created_at,end_meta")
            ->join("oa_report b", "a.report_id=b.id", "left")
            ->join("oa_user u", "b.report_uid=u.uid", "left")
            ->where($where)
            ->group("a.id")
            ->select();

        foreach ($res as $k => $v) {
            $u = Db::name("user")->field('nickname')->where('uid', $v['end_uid'])->find();
            $res[$k]['end_nickname'] = $u['nickname'] ?? "";
            unset($res[$k]['end_uid']);
        }

        $count = Db::name("report_amount")
            ->alias('a')
            ->field("a.id")
            ->join("oa_report b", "a.report_id=b.id", "left")
            ->where($where)
            ->group("a.id")
            ->count();

        Json::msg(200, '获取成功', $res, ['count' => $count]);
    }

    public function del()
    {
        $id = $this->data['id'];
        $idArr = explode(',', $id);
        $a = 1;
        foreach ($idArr as $v) {
            $res = Db::name("report_amount")->where('id', $v)->find();
            if (!$res) {
                $a = 0;
                break;
            }
            $row = Db::name("report_amount")->where('id', $v)->delete();
            if (!$row) {
                $a = 0;
                break;
            } else {
                // 更新当前不收费 并清零
                $arr = [
                    'charge' => 0,
                    'really_amount' => 0,
                    'bil_amount' => 0,
                    'end_uid' => '',
                    'end_created_at' => '',
                    'end_meta' => '',
                    'end_before_amount' => 0
                ];
                Db::name("report")->where('id', $res['report_id'])->update($arr);

                // 删除当前项目所有收款信息
                Db::name("report_amount_info")
                    ->where('report_id', $res['report_id'])
                    ->delete();
            }
        }

        $this->rJson($a);
    }

    public function getInfoList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        $where[] = ["d.id", '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::name("report")
            ->alias("d")
            ->field("d.id,d.name,d.report_text,t.name as report_type_name,u.nickname as report_nickname")
            ->join("oa_user u", "d.report_uid=u.uid", "left")
            ->join("oa_report_type t", "d.report_type=t.id")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();
        $count = Db::name("report")
            ->alias("d")
            ->field("id")
            ->where($where)
            ->count();

        Json::msg(200, '获取成功', $row, ['count' => $count]);
    }

    public function add()
    {
        $data = $this->data;

        $checkArr = [
            'report_id' => 'required',
            'really_amount' => 'required',
            'bil_amount' => 'required',
//            'meta' => 'required'
            'before_amount' => 'require'
        ];
        Validator::check($data, $checkArr);

        $data['created_at'] = date("Y-m-d H:i:s", time());
        $data['uid'] = $this->uid;

        Db::name("report_amount_info")->insert($data);

        $report = Db::name("report_amount")->where("report_id", $data['report_id'])->find();
        if (!$report) {
            Db::name("report_amount")->insert(['report_id' => $data['report_id']]);
        }
        $row = Db::name("report")
            ->where('id', $data['report_id'])
            ->update([
                'end_uid' => $this->uid,
                'end_created_at' => date("Y-m-d H:i:s", time()),
                'charge' => 1,
                'end_meta' => $data['meta'],
            ]);
        Db::name("report")->where('id', $data['report_id'])->setInc('really_amount', $data['really_amount']);
        Db::name("report")->where('id', $data['report_id'])->setInc('bil_amount', $data['bil_amount']);
        Db::name("report")->where('id', $data['report_id'])->setInc('end_before_amount', $data['before_amount']);

        $this->rJson($row);


    }

    public function logList() {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        $where[] = ["d.report_id", '=', $data['id']];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::name("report_amount_info")
            ->alias("d")
            ->field("d.id,d.really_amount,b.report_text,d.bil_amount,d.before_amount,d.meta,d.created_at")
            ->join("oa_report b", "d.report_id=b.id")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();
        $count = Db::name("report_amount_info")
            ->alias("d")
            ->field("id")
            ->where($where)
            ->count();

        Json::msg(200, '获取成功', $row, ['count' => $count]);
    }

}