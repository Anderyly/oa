<?php
/**
 * 财务项目
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2021
 */


namespace app\api\controller;

use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Validator;

class FinanceProject extends Common
{

    public function getList()
    {

        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        $where[] = ['a.id', '!=', 0];

        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['b.name', 'LIKE', '%' . $data['key'] . '%'];
        }

        $res = Db::name("project_amount")
            ->alias('a')
//            ->field("b.name,c.name as project_type_name,b.client,b.contacts,b.phone,u.nickname,b.manager,b.created_at")
            ->field("a.id,b.name,c.name as project_type_name,u.nickname as manager_nickname,b.really_amount,b.bil_amount,end_uid,end_created_at,end_meta")
            ->join("oa_project b", "a.project_id=b.id", "left")
            ->join("oa_user u", "b.manager=u.uid", "left")
            ->join("oa_project_class c", "b.project_type=c.type", "left")
            ->where($where)
            ->group("a.id")
            ->select();

        foreach ($res as $k => $v) {
            $u = Db::name("user")->field('nickname')->where('uid', $v['end_uid'])->find();
            $res[$k]['end_nickname'] = $u['nickname'] ?? "";
            unset($res[$k]['end_uid']);
        }

        $count = Db::name("project_amount")
            ->alias('a')
            ->field("a.id")
            ->join("oa_project b", "a.project_id=b.id", "left")
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
            $res = Db::name("project_amount")->where('id', $v)->find();
            if (!$res) {
                $a = 0;
                break;
            }
            $row = Db::name("project_amount")->where('id', $v)->delete();
            if (!$row) {
                $a = 0;
                break;
            } else {
                // 更新当前不收费 并清零
                $arr = [
                    'charge' => 0,
                    'really_amount' => '',
                    'bil_amount' => '',
                    'end_uid' => '',
                    'end_created_at' => '',
                    'end_meta' => ''
                ];
                Db::name("project")->where('id', $res['project_id'])->update($arr);

                // 删除当前项目所有收款信息
                Db::name("project_amount_info")
                    ->where('project_id', $res['project_id'])
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
        $row = Db::name("project")
            ->alias("d")
            ->field("d.id,d.name,d.client,d.contacts,d.phone,u.nickname as manager_nickname")
            ->join("oa_user u", "d.manager=u.uid", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();
        $count = Db::name("project")
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
            'project_id' => 'required',
            'really_amount' => 'required',
            'bil_amount' => 'required',
//            'meta' => 'required'
        ];
        Validator::check($data, $checkArr);

        $data['created_at'] = date("Y-m-d H:i:s", time());
        $data['uid'] = $this->uid;

        Db::name("project_amount_info")->insert($data);

        $project = Db::name("project_amount")->where("project_id", $data['project_id'])->find();
        if (!$project) {
            Db::name("project_amount")->insert(['project_id' => $data['project_id']]);
        }

        $row = Db::name("project")
            ->where('id', $data['project_id'])
            ->update([
                'end_uid' => $this->uid,
                'end_created_at' => date("Y-m-d H:i:s", time()),
                'charge' => 1,
                'end_meta' => $data['meta']
            ]);
        Db::name("project")->where('id', $data['project_id'])->setInc('really_amount', $data['really_amount']);
        Db::name("project")->where('id', $data['project_id'])->setInc('bil_amount', $data['bil_amount']);

        $this->rJson($row);


    }

}