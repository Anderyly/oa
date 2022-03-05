<?php
/**
 * 结案 - 项目
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

class ProjectClose extends Common
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

//        if (isset($data['type'])) $where[] = ["d.type", '=', $data['type']];

        $where[] = ["d.xm_type", '=', 1];
        $where[] = ["d.status", '=', 1];
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
        $where[] = ["d.status", '=', 1];
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

        $asd = [
            'status' => 2,
        ];
        Db::table($this->table)->where('id', $data['id'])->update($asd);

        $this->rJson($res);
    }

}