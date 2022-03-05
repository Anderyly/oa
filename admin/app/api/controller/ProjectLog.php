<?php
/**
 * 项目工作周志
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

class ProjectLog extends Common
{

    private string $table = "oa_project_log";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        $where[] = ["d.project_id", '=', $data['project_id'] ?? ""];

        $row = Db::table($this->table)
            ->alias("d")
            ->field("d.*,u.nickname")
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

        $arr = [
            'project_id' => $data['project_id'],
            'content' => $_POST['content'],
            'uid' => $this->uid,
            'created_at' => date("Y-m-d H:i:s", time())
        ];

        $kao = Db::table($this->table)->insert($arr);

        $this->rJson($kao);

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
}