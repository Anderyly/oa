<?php
/**
 * 项目报告
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

class ProjectReport extends Common
{

    private string $table = "oa_project_report";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        $where[] = ["d.project_id", '=', $data['project_id'] ?? ""];

        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("d")
            ->field("d.id,d.name,d.created_at,u.nickname,d.status")
            ->join("oa_user u", "d.uid=u.uid", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();

        foreach ($row as $k => $v) {
            $verify = Db::name("project_report_verify")
                ->alias("a")
                ->field("u.nickname,a.status")
                ->join("oa_user u", "a.uid=u.uid")
                ->where("a.rid", $v['id'])
                ->order("a.id desc")
                ->find();
            $row[$k]['verify_nickname'] = $verify['nickname'] ?? "";
        }

        $count = Db::table($this->table)
            ->alias("d")
            ->field($this->field)
            ->where($where)
            ->count();

        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    /**
     * 发起报告
     * @return void
     * @throws Exception
     */
    public function add()
    {
        $data = $this->data;

        $checkArr = [
            'project_id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $row = Db::name("project")->where('id', $data['project_id'])->find();

        if (!$row) {
            Json::msg(400, '项目不存在');
        }

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
            'rid' => $data['id'],
            'status' => $data['status'],
            'uid' => $this->uid,
            'content' => $data['content'],
            'created_at' => date("Y-m-d H:i:s", time()),
        ];
        $res = Db::name("project_report_verify")->insert($arr);

        $this->rJson($res);

    }

    public function detail()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
        ];
        Validator::check($data, $checkArr);

        $res = Db::table($this->table)
            ->alias('a')
            ->field("a.id,a.name,a.partake_uid,a.auxiliary_uid,a.report,a.file,u.nickname,a.created_at")
            ->join("oa_user u", "a.uid=u.uid", "left")
            ->where("a.id", $data['id'])
            ->find();

        if (!$res) Json::msg(400, '数据不存在');


        if (!empty($res['file'])) {
            $ziliaoArr = explode(',', $res['file']);
            foreach ($ziliaoArr as $v1) {
                $res['file_data'][] = Db::name("upload")
                    ->field('id,name,ext,url')
                    ->where('id', $v1)
                    ->find();
            }
        }

        if (!empty($res['report'])) {
            $ziliaoArr = explode(',', $res['report']);
            foreach ($ziliaoArr as $v1) {
                $res['report_data'][] = Db::name("upload")
                    ->field('id,name,ext,url')
                    ->where('id', $v1)
                    ->find();
            }
        }

        $res['verify'] = Db::name("project_report_verify")
            ->alias("a")
            ->field("a.content,a.status,a.created_at,u.nickname")
            ->join("oa_user u", "a.uid=u.uid", "left")
            ->where("a.rid", $res['id'])
            ->select();


        Json::msg(200, 'success', $res);

    }

    public function getAll()
    {
        $res = Db::table($this->table)
            ->field("id ,name as text")
            ->select();

        Json::msg(200, 'success', $res);
    }

}