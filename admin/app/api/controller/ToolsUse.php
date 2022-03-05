<?php
/**
 * 工具使用
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

class ToolsUse extends Common
{

    private string $table = "oa_tools_use";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['status']) and !empty($data['status'])) $where[] = ["d.status", '=', $data['status']];
        if (isset($data['uid'])) $where[] = ["d.uid", '=', $this->uid];

        $where[] = ["d." . $this->field, '!=', 0];
//        if (isset($data['key']) and !empty($data['key'])) {
//            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
//        }
        $row = Db::table($this->table)
            ->alias("d")
            ->field("d.*,u.nickname")
            ->join("oa_user u", "d.uid=u.uid", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();


        foreach ($row as $k => $v) {
            $str = '';
            $as = explode(',', $v['tools_id']);
            foreach ($as as $v1) {
                $tool = Db::table("oa_tools")
                    ->field('name')
                    ->where('id', $v1)
                    ->find();
                $str .= $tool['name'] ?? "" . ",";
            }
            $row[$k]['toolName'] = $str;

        }

        $count = Db::table($this->table)
            ->alias("d")
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

        if (isset($data['status']) and !empty($data['status'])) $where[] = ["d.status", '=', $data['status']];
        $where[] = ["d.uid", '=', $this->uid];
        
//        if (isset($data['key']) and !empty($data['key'])) {
//            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
//        }
        $row = Db::table($this->table)
            ->alias("d")
            ->field("d.*,u.nickname")
            ->join("oa_user u", "d.uid=u.uid", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();


        foreach ($row as $k => $v) {
            $str = '';
            $as = explode(',', $v['tools_id']);
            foreach ($as as $v1) {
                $tool = Db::table("oa_tools")
                    ->field('name')
                    ->where('id', $v1)
                    ->find();
                $str .= $tool['name'] ?? "" . ",";
            }
            $row[$k]['toolName'] = $str;

        }

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
            'uid' => 'require',
            'type' => 'require',
            'tools_id' => 'require',
            'use_at' => 'require',
//            'meta' => 'require',
        ];
        Validator::check($data, $checkArr);

        $data['created_at'] = time();

        $res = Db::table($this->table)->insert($data);

        if ($res) {
            // 申请使用工具时，设置工具使用状态
            $as = explode(',', $data['tools_id']);
            foreach ($as as $v) {
                Db::table("oa_tools")->where('id', $v)->update(['status' => 1]);
            }
        }

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

    /**
     * 操作
     */
    public function option()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'require',
            'status' => 'require',
        ];
        Validator::check($data, $checkArr);

        $res = Db::table($this->table)
            ->where('id', $data['id'])
            ->find();
        if (!$res) Json::msg(400, '数据不存在');

        if ($data['status'] == 4 and $this->uid != $res['uid']) {
            Json::msg(400, '不是本人，无法还回');
        }

        $arr = ['status' => $data['status']];

        if ($data['status'] == 5) {
            $arr['h_at'] = date('Y-m-d H:i:s', time());
            $arr['h_approval_uid'] = $this->uid;
        }

        $as = explode(',', $res['tools_id']);

        if ($res and ($data['status'] == 2 or $data['status'] == 3)) {
            $arr['approval_at'] = date('Y-m-d H:i:s', time());
            $arr['approval_uid'] = $this->uid;
        }

        $res = Db::table($this->table)
            ->where('id', $data['id'])
            ->update($arr);

        // 确定换回工具时 则清零工具使用状态
        if ($res and $data['status'] == 5) {
            foreach ($as as $v) {
                Db::table("oa_tools")->where('id', $v)->update(['status' => 0]);
            }

        }

        // 确定审核通过时
        if ($res and $data['status'] == 2) {
            foreach ($as as $v) {
                Db::table("oa_tools")->where('id', $v)->update(['status' => 2]);
            }

        }

        // 确定审核不通过时
        if ($res and $data['status'] == 3) {
            foreach ($as as $v) {
                Db::table("oa_tools")->where('id', $v)->update(['status' => 0]);
            }

        }

        // 正在归还时
        if ($res and $data['status'] == 4) {
            foreach ($as as $v) {
                Db::table("oa_tools")->where('id', $v)->update(['status' => 3]);
            }

        }

        $this->rJson($res);


    }

}