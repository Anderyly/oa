<?php
/**
 * 案例
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2021
 */

namespace app\api\controller;

use ay\lib\Db;
use ay\lib\Json;
use ay\lib\Upload;
use ay\lib\Validator;
use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Cases extends Common
{

    private string $table = "oa_case";
    private string $field = 'id';

    public function getList()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;
        if (isset($data['t']) and !empty($data['t']) and $data['t'] != "all") $where[] = ["l.uid", '=', $data['t']];
        $where[] = ["l." . $this->field, '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['l.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table($this->table)
            ->alias("l")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("l.id desc")
            ->select();

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
            'name' => 'required'
        ];
        Validator::check($data, $checkArr);

        $data['created_at'] = date("Y-m-d H:i:s", time());
        $data['uid'] = $this->uid;

        $res = Db::table($this->table)->insert($data);

        $this->rJson($res);

    }

    public function edit()
    {
        $data = $this->data;

        $checkArr = [
            'id' => 'required',
            'name' => 'required'
        ];
        Validator::check($data, $checkArr);

        $id = $data['id'];
        unset($data['id']);

        $res = Db::table($this->table)->where('id', $id)->update($data);

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
            if (!empty($res['data'])) {
                $ziliaoArr = explode(',', $res['data']);
                foreach ($ziliaoArr as $v1) {
                    $res['zi_liao'][] = Db::name("upload")
                        ->field('id,name,ext,url,size')
                        ->where('id', $v1)
                        ->find();
                }
            }

            if (!empty($res['cases'])) {
                $ziliaoArr = explode(',', $res['cases']);
                foreach ($ziliaoArr as $v1) {
                    $res['an_li'][] = Db::name("upload")
                        ->field('id,name,ext,url,size')
                        ->where('id', $v1)
                        ->find();
                }
            }
            Json::msg(200, 'success', $res);
        }
    }

    public function import()
    {

        $upload = new Upload(PUB . '/upload/cases/', ['xlsx','xls']);
        $res = $upload->operate('file');
        if (!isset($res['path'])) Json::msg(400, '上传错误');

        $reader = new Xlsx();

        $spreadsheet = $reader->load($res['path']);
        $sheet = $spreadsheet->getActiveSheet();
        $data = [];
        foreach ($sheet->getRowIterator(2) as $row) {
            $tmp = [];
            foreach ($row->getCellIterator() as $cell) {
                $tmp[] = $cell->getFormattedValue();
            }

            $data = [
                'name' =>$tmp[0],
                'unit' =>$tmp[1],
                'position' =>$tmp[2],
                'land_type' =>$tmp[3],
                'building' =>$tmp[4],
                'completion' =>$tmp[5],
                'all_floors' =>$tmp[6],
                'orientation' =>$tmp[7],
                'built_area' =>$tmp[8],
                'decoration' =>$tmp[9],
                'renovation_amount' =>$tmp[10],
                'correct_amount' =>$tmp[11],
                'transaction_date' =>$tmp[12],
                'case_source' =>$tmp[13],
                'building_number' =>$tmp[14],
                'room_number' =>$tmp[15],
                'property_type' =>$tmp[16],
                'remaining_service_life' =>$tmp[17],
                'property_situation' =>$tmp[18],
                'floors' =>$tmp[19],
                'house_type' =>$tmp[20],
                'face_south' =>$tmp[21],
                'transaction_amount' =>$tmp[22],
                'taxation' =>$tmp[23],
                'univalence' =>$tmp[24],
            ];
            break;
        }

        Json::msg(200, '导入成功', $data);

    }

}