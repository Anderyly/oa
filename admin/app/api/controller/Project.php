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

class Project extends Common
{

    private string $table = "oa_project";
    private string $field = 'id';

    public function gxList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['project_type']) and !empty($data['project_type'])) $where[] = ["d.project_type", '=', $data['project_type']];
        if (isset($data['charge']) and $data['charge'] != "") $where[] = ["d.charge", '=', $data['charge']];

        $where[] = ["d.xm_type", '=', 1];
        $where[] = ["d.status", '=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("d")
            ->field("d.id,d.name,d.client,d.contacts,d.phone,u.nickname as manager_nickname,d.charge")
            ->join("oa_user u", "d.manager=u.uid", "left")
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

    public function zsList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

//        if (isset($data['type'])) $where[] = ["d.type", '=', $data['type']];
        if (isset($data['project_type']) and !empty($data['project_type'])) $where[] = ["d.project_type", '=', $data['project_type']];
        if (isset($data['charge']) and $data['charge'] != "") $where[] = ["d.charge", '=', $data['charge']];

        $where[] = ["d.xm_type", '=', 2];
        $where[] = ["d.status", '=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("d")
            ->field("d.id,d.name,d.client,d.contacts,d.phone,u.nickname as manager_nickname,d.charge")
            ->join("oa_user u", "d.manager=u.uid", "left")
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

    public function allList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

//        if (isset($data['type'])) $where[] = ["d.type", '=', $data['type']];
        if (isset($data['project_type']) and !empty($data['project_type'])) $where[] = ["d.project_type", '=', $data['project_type']];
        if (isset($data['charge']) and $data['charge'] != "") $where[] = ["d.charge", '=', $data['charge']];

        $where[] = ["d.id", '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("d")
            ->field("d.id,d.name,d.client,d.contacts,d.phone,u.nickname as manager_nickname,d.charge")
            ->join("oa_user u", "d.manager=u.uid", "left")
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

    public function otherList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

//        if (isset($data['type'])) $where[] = ["d.type", '=', $data['type']];
        if (isset($data['project_type']) and !empty($data['project_type'])) $where[] = ["d.project_type", '=', $data['project_type']];
        if (isset($data['charge']) and $data['charge'] != "") $where[] = ["d.charge", '=', $data['charge']];

        $where[] = ["d.xm_type", '=', 3];
        $where[] = ["d.status", '=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("d")
            ->field("d.id,d.name,d.client,d.contacts,d.phone,u.nickname as manager_nickname,d.charge")
            ->join("oa_user u", "d.manager=u.uid", "left")
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
     * 发起项目
     * @return void
     * @throws Exception
     */
    public function add()
    {
        $data = $this->data;

        $checkArr = [
            'name' => 'required',
            'project_type' => 'required',
            'client' => 'required',
            'contacts' => 'required',
            'phone' => 'required',
            'manager' => 'required'
        ];
        Validator::check($data, $checkArr);

        $row = Db::name("project_class")->where('id', $data['project_type'])->find();
        if (!$row) {
            Json::msg(400, '项目类型不存在');
        }

        $data['xm_type'] = $row['type'];

//        $data['project_type'] = $row['type'];
        $data['created_at'] = date("Y-m-d H:i:s", time());
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

    public function editInfo()
    {
        $data = $this->data;

        $checkArr = [
            'project_id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $info = Db::name("project_info")
            ->field('id,uid')
            ->where('project_id', $data['project_id'])
            ->find();
//        if ($info and $info['uid'] != $this->uid) Json::msg(400, '不是您的项目');

        if (!isset($info['id'])) {
            $data['info_created_at'] = date("Y-m-d H:i:s", time());
            $data['uid'] = $this->uid;
            $res = Db::name("project_info")->insert($data);
        } else {
            unset($data['project_id']);
            $res = Db::name("project_info")->where('id', $info['id'])->update($data);
        }

        $this->rJson($res);


    }

    public function detail()
    {
        $id = $this->data['project_id'];
        $res = Db::table($this->table)
            ->alias('a')
            ->field("a.id as project_id,a.name,c.name as project_type_name,a.client,a.contacts,a.phone,u.nickname,a.manager,a.created_at")
            ->join("oa_user u", "a.uid=u.uid", "left")
            ->join("oa_project_class c", "a.project_type=c.id", "left")
            ->where("a.id", $id)
            ->find();

        if (!$res) Json::msg(400, '数据不存在');

        $manager = Db::name("user")->field("nickname")->where("uid", $res['manager'])->find();
        $res['manager_nickname'] = $manager['nickname'] ?? "";
        unset($res['manager']);

        $arr['content'] = $res;
        $info = Db::name("project_info")
            ->alias("a")
            ->field("a.*,u.nickname")
            ->join("oa_user u", "a.uid=u.uid", "left")
            ->where('a.project_id', $res['project_id'])
            ->find();

        if (!$info) $info['partake_uid'] = '';

        $word = Db::name("user")
            ->field("nickname")
            ->where('uid', $info['word_uid'])
            ->find();
        $info['word_nickname'] = $word['nickname'] ?? "";

        $file = Db::name("user")
            ->field("nickname")
            ->where('uid', $info['file_uid'])
            ->find();
        $info['file_nickname'] = $file['nickname'] ?? "";


//        $partake_uid = explode(',', $info['partake_uid']);
//        foreach ($partake_uid as $k => $v) {
//            $user = Db::name("user")
//                ->field("uid,nickname")
//                ->where('uid', $v)
//                ->find();
//            $info['partake_data'][$k] = $user;
//        }

        $arr['info'] = $info;

        Json::msg(200, 'success', $arr);

    }

    public function infoDetail()
    {
        $id = $this->data['project_id'];

        $info = Db::name("project_info")
            ->alias("a")
            ->field("a.*,u.nickname")
            ->join("oa_user u", "a.uid=u.uid", "left")
            ->where('a.project_id', $id)
            ->find();

        $word = Db::name("user")
            ->field("nickname")
            ->where('uid', $info['word_uid'])
            ->find();
        $info['word_nickname'] = $word['nickname'] ?? "";

        $file = Db::name("user")
            ->field("nickname")
            ->where('uid', $info['file_uid'])
            ->find();
        $info['file_nickname'] = $file['nickname'] ?? "";


        $partake_uid = explode(',', $info['partake_uid']);
        foreach ($partake_uid as $k => $v) {
            $user = Db::name("user")
                ->field("uid,nickname")
                ->where('uid', $v)
                ->find();
            $info['partake_data'] .= $user['nickname'] . ',';
            $info['partake_data1'][] = $user;
        }

        $info['partake_data'] = rtrim($info['partake_data'], ',');

        if ($info['nature'] == 1) {
            $info['nature'] = "年度";
        } else {
            $info['nature'] = "独立";
        }

        $info['auxiliary_data'] = explode(',', $info['auxiliary']);


        Json::msg(200, 'success', $info);

    }

    public function info()
    {
        $id = $this->data['id'];

        // 报告
//        $info = Db::name("project_report")
//            ->alias("a")
//            ->field("a.id,a.name,a.partake_uid,a.created_at,u.nickname")
//            ->join("oa_user u", "a.uid=u.uid", "left")
//            ->where('a.project_id', $id)
//            ->select();

//        foreach ($info as $k => $v) {
//            $str = '';
//            $partake = explode(',', $v['partake_uid']);
//
//            foreach ($partake as $v1) {
//                $u = Db::name("user")->field("nickname")->where("uid", $v1)->find();
//                if ($u) $str .= $u['nickname'] . ',';
//            }
//            $info[$k]['partake_name'] = rtrim($str, ',');
//            unset($info[$k]['partake_uid']);
//
//            $ver = Db::name("project_report_verify")
//                ->alias('a')
//                ->field("u.nickname,a.content,a.created_at")
//                ->join("oa_user u", "a.uid=u.uid", "left")
//                ->order('a.id desc')
//                ->find();
//            $info[$k]['verify_nickname'] = $ver['nickname'] ?? "";
//            $info[$k]['verify_created_at'] = $ver['created_at'] ?? "";
//            $info[$k]['verify_content'] = $ver['content'] ?? "";
//
//        }

        // 工具信息
        $tool_use = Db::name("tools_use")
            ->alias('a')
            ->field("u.nickname,a.use_at,a.h_at,a.tools_id")
            ->join("oa_user u", "a.uid=u.uid", "left")
            ->where('a.project_id', $id)
            ->select();

        foreach ($tool_use as $k => $v) {
            $str = '';
            $as = explode(',', $v['tools_id']);
            foreach ($as as $v1) {
                $tool = Db::table("oa_tools")
                    ->field('name')
                    ->where('id', $v1)
                    ->find();
                $str .= $tool['name'] ?? "" . ",";
            }
            $tool_use[$k]['toolName'] = rtrim($str, ',');
            unset($tool_use[$k]['tools_id']);

        }

        // 日志
        $log = Db::name("project_log")
            ->alias('a')
            ->field("a.content,u.nickname,a.created_at")
            ->join("oa_user u", "a.uid=u.uid", "left")
            ->where('a.project_id', $id)
            ->select();

        // 报销
        $reimbursement = Db::name("reimbursement")
            ->alias('a')
            ->field("a.content,a.amount,u.nickname,a.created_at,a.approval_uid")
            ->join("oa_user u", "a.uid=u.uid", "left")
            ->where('a.project_id', $id)
            ->select();

        foreach ($reimbursement as $k => $v) {
            $approval = Db::name("user")->field("nickname")->where("uid", $v['approval_uid'])->find();
            $reimbursement[$k]['approval_nickname'] = $approval['nivkname'] ?? "";
            unset($reimbursement[$k]['approval_uid']);
            $reimbursement[$k]['created_at'] = date('Y-m-d H:i:s', $v['created_at']);
        }

        // 绩效
//        $achievements = Db::name("project_achievements")
//            ->where('project_id', $id)
//            ->select();
        $tg = Db::name("project_achievements")
            ->field('id')
            ->where('status', 3)
            ->where('project_id', $id)
            ->select();
        $achievements = [
            'bgs' => [],
            'wb' => []
        ];
        foreach ($tg as $v) {
            $bgs = Db::name("project_achievements_info")
                ->alias('a')
                ->field('a.id,a.uid,u.nickname')
                ->join('oa_user u', 'a.uid=u.uid', 'left')
                ->where('a.aid', $v['id'])
                ->where('a.type', 1)
                ->group('a.uid')
                ->select();
            foreach ($bgs as $k1 => $v1) {
                $sql = "SELECT SUM(work) as work,SUM(subsidy) as subsidy,SUM(wages) as wages,SUM(all_work) as all_work FROM `oa_project_achievements_info` WHERE uid=" . $v1['uid'] . " AND aid=" . $v['id'];
                $in = Db::table("s")->doSql($sql);
                $in = $in[0];
                $bgs[$k1]['work'] = $in['work'];
                $bgs[$k1]['subsidy'] = $in['subsidy'];
                $bgs[$k1]['wages'] = $in['wages'];
                $bgs[$k1]['all_work'] = $in['all_work'];
            }
            $achievements['bgs'] = $bgs;

            $wb = Db::name("project_achievements_info")
                ->alias('a')
                ->field('a.id,a.uid')
                ->where('a.aid', $v['id'])
                ->where('a.type', 2)
                ->group('a.uid')
                ->select();
            foreach ($wb as $k1 => $v1) {
                $sql = "SELECT SUM(work) as work,SUM(subsidy) as subsidy,SUM(wages) as wages,SUM(all_work) as all_work FROM `oa_project_achievements_info` WHERE uid=" . $v1['uid'] . " AND aid=" . $v['id'];
                $in = Db::table("s")->doSql($sql);
                $in = $in[0];
                $wb[$k1]['work'] = $in['work'];
                $wb[$k1]['subsidy'] = $in['subsidy'];
                $wb[$k1]['wages'] = $in['wages'];
                $wb[$k1]['all_work'] = $in['all_work'];
            }
            $achievements['wb'] = $wb;

        }


        $arr = [
//            'info' => $info,
            'log' => $log,
            'tools' => $tool_use,
            'reimbursement' => $reimbursement,
            'achievements' => $achievements
        ];


        Json::msg(200, 'success', $arr);

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
        if ($res['report_uid'] == $this->uid) Json::msg(400, '不能给自己结案');

        $asd = [
            'status' => 1,
        ];
        Db::table($this->table)->where('id', $data['id'])->update($asd);

//        $arr = [
//            'report_id' => $data['id'],
//            'type' => 1,
//            'examine_uid' => $this->uid,
//            'examine_date' => date("Y-m-d H:i:s", time()),
//            'examine_type' => 3
//
//        ];
//        $res = Db::name("report_examine")->insert($arr);

        $this->rJson($res);
    }


    public function odata()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::name("upload")
            ->field("id,name,ext,url")
            ->where('type', 2)
            ->where('cid', $data['id'])
            ->select();

        Json::msg(200, 'success', $res);
    }

    /**
     * @throws Exception
     */
    public function delFile()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::name("upload")
            ->where('id', $data['id'])
            ->delete();
        $this->rJson($res);
    }

    public function getAll()
    {
        $res = Db::name("project")
            ->field("id, name as text")
            ->select();

        Json::msg(200, 'success', $res);
    }

}