<?php
/**
 * 部门
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

class Leave extends Common
{

    private string $table = "oa_leave";
    private string $tableClass = "oa_leave_class";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;
        $where[] = ["l." . $this->field, '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['l.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("l")
            ->field('l.*,class.name as className,user.nickname')
            ->join("oa_user user", "l.uid=user.uid", "LEFT")
            ->join($this->tableClass . " class", "l.type=class.id", "LEFT")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("l.id desc")
            ->select();

        foreach ($row as $k => $v) {
            if (empty($v['operate_uid']) or $v['operate_uid'] == 0) continue;
            $user = Db::name("user")->field("nickname")->where('uid', $v['operate_uid'])->find();
            if ($user) {
                $row[$k]['operate_name'] = $user['nickname'];
            }
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
        $where[] = ["l.uid", '=', $this->uid];
        $where[] = ["l." . $this->field, '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['l.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("l")
            ->field('l.*,class.name as className,user.nickname')
            ->join("oa_user user", "l.uid=user.uid", "LEFT")
            ->join($this->tableClass . " class", "l.type=class.id", "LEFT")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("l.id desc")
            ->select();

        foreach ($row as $k => $v) {
            if (empty($v['operate_uid']) or $v['operate_uid'] == 0) continue;
            $user = Db::name("user")->field("nickname")->where('uid', $v['operate_uid'])->find();
            if ($user) {
                $row[$k]['operate_name'] = $user['nickname'];
            }
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
            'type' => 'require',
            'duration' => 'require',
            'start_time' => 'require',
            'end_time' => 'require',
            'uid' => 'require',
            'content' => 'require',
        ];
        Validator::check($data, $checkArr);

        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        $data['created_at'] = time();
        $data['status'] = 1;

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

    public function detail()
    {
        $id = $this->data['id'];
        $res = Db::table($this->table)
            ->field("l.*,user.nickname")
            ->alias("l")
            ->join("oa_user user", "l.uid=user.uid", "left")
            ->where("l.id", $id)
            ->find();
        if (!$res) {
            Json::msg(400, '数据不存在');
        } else {
            $res['start_time'] = date("Y-m-d", $res['start_time']) . ' 00:00:00';
            $res['end_time'] = date("Y-m-d", $res['end_time']) . ' 00:00:00';
            $res['created_at'] = date("Y-m-d H:i:s", $res['created_at']);
            $res['operate_at'] = date("Y-m-d H:i:s", $res['operate_at']);
            if (!empty($res['operate_uid']) and $res['status'] == 2) {
                $us = Db::table('oa_user')->field("nickname")->where('uid', $res['operate_uid'])->find();
                if ($us) {
                    $res['opName'] = $us['nickname'];
                } else {
                    $res['opName'] = '用户已删除';
                }
            }
            Json::msg(200, 'success', $res);
        }
    }

    public function edit()
    {
        $data = $this->data;

        $id = $data['id'];
        unset($data['id']);

        if (isset($data['operate_uid'])) {
            $arr = [
                'operate_uid' => $data['operate_uid'],
                'meta' => $data['meta'],
                'operate_at' => time(),
                'status' => $data['status']
            ];
        } else {
            if (strlen($data['start_time'])) $data['start_time'] = strtotime($data['start_time']);
            if (strlen($data['end_time'])) $data['end_time'] = strtotime($data['end_time']);
            $arr = $data;
        }

        $res = Db::table($this->table)->where("id", $id)->update($arr);

        $this->rJson($res);

    }

    public function verify()
    {
        $data = $this->data;

        $id = $data['id'];
        unset($data['id']);

        if (isset($data['operate_uid'])) {
            $arr = [
                'operate_uid' => $data['operate_uid'],
                'meta' => $data['meta'],
                'operate_at' => time(),
                'status' => $data['status']
            ];
        } else {
            if (strlen($data['start_time'])) $data['start_time'] = strtotime($data['start_time']);
            if (strlen($data['end_time'])) $data['end_time'] = strtotime($data['end_time']);
            $arr = $data;
        }

        $res = Db::table($this->table)->where("id", $id)->update($arr);

        $this->rJson($res);

    }

    public function getAll()
    {
        $res = Db::table($this->table)
            ->field("id, name as text")
            ->select();

        Json::msg(200, 'success', $res);
    }

    public function getClassAll()
    {
        $res = Db::name("leave_class")
            ->field("id as value, name as title")
            ->select();

        Json::msg(200, 'success', $res);
    }

}