<?php
/**
 * logo
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

class Logo extends Common
{

    private string $table = "oa_logo";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        $row = Db::table($this->table)
            ->limit($data['page'], $data['rows'])
            ->order("id desc")
            ->select();

        foreach ($row as $k => $v) {
            $row[$k]['url'] = URL . $v['url'];
        }

        $count = Db::table($this->table)
            ->field($this->field)
            ->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    public function set()
    {
        $data = $this->data;

        $res = Db::table($this->table)->where('id', $data['id'])->find();
        if (!$res) Json::msg(400, '数据错误');

        Db::table($this->table)->where('id', '!=', 0)->update(['type' => 0]);
        Db::table($this->table)->where('id', $data['id'])->update(['type' => 1]);
        $row = Db::table("ay_config")->where('k', 'logo')->update(['v' => $res['url']]);
        $this->rJson($row);
    }

    public function get() {
        $row = Db::table("ay_config")->where('k', 'logo')->find();
        Json::msg(200, 'success', ['url' => URL . $row['v']]);
    }

}