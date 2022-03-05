<?php
/**
 * 项目绩效分配
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

class ProjectAchievements extends Common
{

    private string $table = "oa_project_achievements";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        $where[] = ["d.project_id", '=', $data['project_id'] ?? ""];
        $where[] = ["d.status", '!=', 1];

        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("d")
            ->field("d.id,d.total_work,d.total_subsidy,d.total_wages,d.total_all_work,d.status,u.nickname,d.created_at")
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

    /**
     * 新增
     * @return void
     * @throws Exception
     */
    public function add()
    {
        $data = $_POST;

        $checkArr = [
            'project_id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $row = Db::name("project")->where('id', $data['project_id'])->find();

        if (!$row) {
            Json::msg(400, '项目不存在');
        }

        $arr = [
            'project_id' => $data['project_id'],
            'status' => $data['status'],
            'uid' => $this->uid,
            'created_at' => date("Y-m-d H:i:s", time())
        ];

        $kao = Db::table($this->table)->insert($arr);

        $json = json_decode($data['data'], true);

        $insertArr = [];
        foreach ($json as $v) {
            $insertArr[] = [
                'type' => $v['type'],
                'work' => $v['work'],
                'subsidy' => $v['subsidy'],
                'wages' => $v['wages'],
                'all_work' => $v['all_work'],
                'uid' => $v['uid'],
                'project_id' => $data['project_id'],
                'aid' => $kao,
//                'created_at' => date("Y-m-d H:i:s", time())
            ];
        }
//        dump($insertArr);exit;

        $res = Db::name("project_achievements_info")->insertAll($insertArr);

        $sql = "SELECT SUM(work) as work,SUM(subsidy) as subsidy,SUM(wages) as wages,SUM(all_work) as all_work FROM `oa_project_achievements_info` WHERE aid=" . $kao;
//            echo $sql;
        $in = Db::table("s")->doSql($sql);
        $in = $in[0];
//            dump($in);

        $sql = "UPDATE `oa_project_achievements` SET `total_work`=`total_work`+" . $in['work'] .
            ",`total_subsidy`=`total_subsidy`+" . $in['subsidy'] .
            ",`total_wages`=`total_wages`+" . $in['wages'] .
            ",`total_all_work`=`total_all_work`+" . $in['all_work'] .
            " WHERE id=" . $kao;
//            echo $sql;exit;
        Db::table($this->table)->doSql($sql);

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

    public function edit()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $info = Db::table($this->table)
            ->where('id', $data['id'])
            ->find();
        if (!$info) Json::msg(400, '项目报告不存在');

        $id = $data['id'];
        unset($data['id']);

        $res = Db::table($this->table)->where('id', $id)->update($data);

        $this->rJson($res);

    }

    public function verify()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
            'status' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::table($this->table)->where('id', $data['id'])->find();
        if (!$res) Json::msg(400, '数据不存在');
        if ($res['status'] != 2) Json::msg(400, '当前不是待审核状态');
//        if ($res['uid'] == $this->uid) Json::msg(400, '不能给自己审核');

        if ($data['status'] == 1) {
            $asd = [
                'status' => 3,
            ];
        } else {
            $asd = [
                'status' => 4,
            ];
        }

        Db::table($this->table)->where('id', $data['id'])->update($asd);

        $arr = [
            'ach_id' => $data['id'],
            'status' => $data['status'],
            'uid' => $this->uid,
            'content' => $data['content'],
            'created_at' => date("Y-m-d H:i:s", time()),
        ];
        $res = Db::name("project_achievements_verify")->insert($arr);

        $this->rJson($res);

    }

    public function info()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res['info'] = Db::name("project_achievements_info")
            ->alias("a")
            ->field("a.id,a.uid,a.type,a.work,a.subsidy,a.wages,a.all_work,u.nickname")
            ->join("oa_user u", "a.uid=u.uid and a.type!=2", "left")
            ->where("a.aid", $data['id'])
            ->select();

        $res['verify'] = Db::name("project_achievements_verify")
            ->alias("a")
            ->field("a.content,a.status,a.created_at,u.nickname")
            ->join("oa_user u", "a.uid=u.uid", "left")
            ->where("a.ach_id", $data['id'])
            ->select();


        Json::msg(200, 'success', $res);

    }

    public function getUser()
    {
        $data = $this->data;

        $checkArr = [
            'project_id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::table("oa_project_info")
            ->field("partake_uid,auxiliary")
            ->where("project_id", $data['project_id'])
            ->find();

        $partake = explode(',', $res['partake_uid']);

        foreach ($partake as $k => $v) {
            $u = Db::name("user")->field('nickname')->where('uid', $v)->find();
            if (!$u) continue;
            $arr['partake'][] = [
                'uid' => $v,
                'nickname' => $u['nickname']
            ];
        }

        $auxiliary = explode(',', $res['auxiliary']);

        foreach ($auxiliary as $k => $v) {
            $arr['auxiliary'][] = [
                'uid' => $data['project_id'] . '_' . $k,
                'nickname' => $v
            ];
        }

        Json::msg(200, 'success', $arr);
    }

}