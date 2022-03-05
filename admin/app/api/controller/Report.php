<?php
/**
 * 报告
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

class Report extends Common
{

    private string $table = "oa_report";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['status']) and $data['status'] != "") $where[] = ["l.status", '=', $data['status']];
        if (isset($data['report_type']) and !empty($data['report_type'])) $where[] = ["l.report_type", '=', $data['report_type']];
        if (isset($data['charge']) and $data['charge'] != "") $where[] = ["l.charge", '=', $data['charge']];

        $where[] = ["l.fgs", '=', 0];
        $where[] = ["l.ybg", '=', 0];
        $where[] = ["l.status", '!=', 5];
        $where[] = ["l.status", '!=', 6];
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

        if (isset($data['status']) and $data['status'] != "") $where[] = ["l.status", '=', $data['status']];
        if (isset($data['report_type']) and !empty($data['report_type'])) $where[] = ["l.report_type", '=', $data['report_type']];
        if (isset($data['charge']) and $data['charge'] != "") $where[] = ["l.charge", '=', $data['charge']];

        $where[] = ["l.fgs", '=', 1];
        $where[] = ["l.status", '!=', 5];
        $where[] = ["l.status", '!=', 6];
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
    public function getAll()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['status']) and $data['status'] != "") $where[] = ["l.status", '=', $data['status']];
        if (isset($data['report_type']) and !empty($data['report_type'])) $where[] = ["l.report_type", '=', $data['report_type']];
        if (isset($data['charge']) and $data['charge'] != "") $where[] = ["l.charge", '=', $data['charge']];

        $where[] = ["l.id", '!=', 0];
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

    public function beforeList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['status']) and $data['status'] != "") $where[] = ["l.status", '=', $data['status']];
        if (isset($data['report_type']) and !empty($data['report_type'])) $where[] = ["l.report_type", '=', $data['report_type']];
        if (isset($data['charge']) and $data['charge'] != "") $where[] = ["l.charge", '=', $data['charge']];

        $where[] = ["l.ybg", '=', 1];
        $where[] = ["l.status", '!=', 5];
        $where[] = ["l.status", '!=', 6];
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

    public function myList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['status']) and !empty($data['status'])) $where[] = ["l.status", '=', $data['status']];

        $where[] = ["l.uid", '=', $this->uid];
        $where[] = ["l.status", '!=', 5];
        $where[] = ["l.status", '!=', 6];
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

    public function add()
    {
        $data = $this->data;

        $checkArr = [
            'name' => 'required',
            'report_type' => 'required',
            'report_text' => 'required',
            'benchmark_date' => 'required',
            'report_uid' => 'required',
        ];
        Validator::check($data, $checkArr);

        $row = Db::name("report_type")->where('id', $data['report_type'])->find();
        if (!$row) {
            Json::msg(400, '报告类型不存在');
        }
        if ($row['company_id'] != 2) {
            $data['fgs'] = 1;
        }
        if ($row['type'] == 2) {
            $data['ybg'] = 1;
        }
        Db::name("report_type")->where('id', $data['report_type'])->setInc("number");

        $data['created_at'] = date("Y-m-d H:i:s", time());
        $data['uid'] = $this->uid;

        $res = Db::table($this->table)->insert($data);

        $this->rJson($res);

    }

    /**
     * 作报告
     * @return void
     * @throws Exception
     */
    public function edit()
    {
        $data = $_POST;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $id = $data['id'];
        unset($data['id']);

        $res = Db::table($this->table)->where('id', $id)->update($data);

        $this->rJson($res);

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
     * 详情
     * @return void
     * @throws Exception
     */
    public function detail()
    {
        $id = $this->data['id'];
        $res = Db::table($this->table)
            ->alias("a")
            ->field("a.*,u.nickname")
            ->join("oa_user u", "a.uid=u.uid")
            ->where("a.id", $id)
            ->find();
        if (!$res) {
            Json::msg(400, '数据不存在');
        } else {
            if (!empty($res['enclosure_file'])) {
                $ziliaoArr = explode(',', $res['enclosure_file']);
                foreach ($ziliaoArr as $v1) {
                    $res['enclosure_file_data'][] = Db::name("upload")
                        ->field('id,name,ext,url,size')
                        ->where('id', $v1)
                        ->find();
                }
            }

            if (!empty($res['report_file'])) {
                $ziliaoArr = explode(',', $res['report_file']);
                foreach ($ziliaoArr as $v1) {
                    $res['report_file_data'][] = Db::name("upload")
                        ->field('id,name,ext,url,size')
                        ->where('id', $v1)
                        ->find();
                }
            }

            if (!empty($res['customer'])) {
//                $ziliaoArr = explode(',', $res['customer']);
//                foreach ($ziliaoArr as $v1) {
//                    $res['customer_data'][] = Db::name("customer")
//                        ->field('id,company_name,user_name,phone')
//                        ->where('id', $v1)
//                        ->find();
//                }
                $res['customer'] = json_decode($res['customer'], true);
            } else {
                $res['customer'] = [];
            }

            $res['verify'] = Db::name("report_examine")
                ->field("examine_uid,content,examine_date,examine_type")
                ->where('report_id', $res['id'])
                ->where('type', 1)
                ->select();
            if (count($res['verify']) >= 1) {
                foreach ($res['verify'] as $k => $v) {
                    $user = Db::name("user")->field("nickname")->where('uid', $v['examine_uid'])->find();
                    $res['verify'][$k]['nickname'] = $user['nickname'] ?? "";
                    unset($res['verify'][$k]['examine_uid']);
                }
            }

            $res['seal'] = Db::name("report_examine")
                ->field("examine_uid,content,examine_date,examine_type")
                ->where('report_id', $res['id'])
                ->where('type', 2)
                ->select();
            if (count($res['seal']) >= 1) {
                foreach ($res['seal'] as $k => $v) {
                    $user = Db::name("user")->field("nickname")->where('uid', $v['examine_uid'])->find();
                    $res['seal'][$k]['nickname'] = $user['nickname'] ?? "";
                    unset($res['seal'][$k]['examine_uid']);
                }
            }

            Json::msg(200, 'success', $res);
        }
    }

    /**
     * 审核
     * @return void
     * @throws Exception
     */
    public function verify()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::table($this->table)->where('id', $data['id'])->find();
        if (!$res) Json::msg(400, '数据不存在');
        if ($res['status'] != 2) Json::msg(400, '当前不是待审核状态');
        if ($res['report_uid'] == $this->uid) Json::msg(400, '不能给自己审核');

        if ($data['status'] == 1) {
            $asd = [
                'status' => 3,
                'report_amount' => $data['report_amount'],
                'report_final_amount' => $data['report_final_amount'],
                'report_quality' => $data['report_quality'],
            ];

        } else {
            $asd  = [
                'status' => 1,
                'report_amount' => $data['report_amount'],
                'report_final_amount' => $data['report_final_amount'],
                'report_quality' => $data['report_quality'],
            ];
        }

        Db::table($this->table)->where('id', $data['id'])->update($asd);

        $arr = [
            'report_id' => $data['id'],
            'type' => 1,
            'examine_uid' => $this->uid,
            'content' => $data['content'],
            'examine_date' => date("Y-m-d H:i:s", time()),
            'examine_type' => $data['status']

        ];
        $res = Db::name("report_examine")->insert($arr);

        $this->rJson($res);

    }


    /**
     * 盖章
     * @return void
     * @throws Exception
     */
    public function seal()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::table($this->table)->where('id', $data['id'])->find();
        if (!$res) Json::msg(400, '数据不存在');
        if ($res['status'] != 3) Json::msg(400, '当前不是待盖章状态');
        if ($res['report_uid'] == $this->uid) Json::msg(400, '不能给自己盖章');

        if ($data['status'] == 1) {
            $asd = [
                'status' => 4,
                'seal_quality' => $data['seal_quality'],
                'seal_final_amount' => $data['seal_final_amount']
            ];
            Db::table($this->table)->where('id', $data['id'])->update($asd);
        }

        $arr = [
            'report_id' => $data['id'],
            'type' => 2,
            'examine_uid' => $this->uid,
            'content' => $data['content'],
            'examine_date' => date("Y-m-d H:i:s", time()),
            'examine_type' => $data['status']

        ];
        $res = Db::name("report_examine")->insert($arr);

        $this->rJson($res);
    }

    /**
     * 结案
     * @return void
     * @throws Exception
     */
    public function close()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::table($this->table)->where('id', $data['id'])->find();
        if (!$res) Json::msg(400, '数据不存在');
        if ($res['status'] != 4) Json::msg(400, '当前不是待结案状态');
        if ($res['report_uid'] == $this->uid) Json::msg(400, '不能给自己结案');

        $asd = [
            'status' => 5,
        ];
        Db::table($this->table)->where('id', $data['id'])->update($asd);

        $arr = [
            'report_id' => $data['id'],
            'type' => 1,
            'examine_uid' => $this->uid,
//            'content' => $data['content'],
            'examine_date' => date("Y-m-d H:i:s", time()),
            'examine_type' => 3

        ];
        $res = Db::name("report_examine")->insert($arr);

        $this->rJson($res);
    }

    public function formal()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::table($this->table)->where('id', $data['id'])->find();
        if (!$res) Json::msg(400, '数据不存在');
        if ($res['ybg'] != 1) Json::msg(400, '当前不是预报告');

        $row = Db::table($this->table)->where('id', $data['id'])->update(['ybg' => 2]);

        $this->rJson($row);

    }

}