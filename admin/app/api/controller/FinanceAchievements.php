<?php
/**
 * 财务 绩效
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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FinanceAchievements extends Common
{

    private string $table = "oa_tools_use";
    private string $field = 'id';


    public function detail()
    {
        $data = $this->data;

        $ui = explode(',', $data['uid']);

        $arr = [];

        foreach ($ui as $k1 => $v) {
            $really_amount = 0;
            $report_final_amount = 0;
            $seal_final_amount = 0;
//            $arr[$k1]['nickname'] = $u['nickname'];
            $sql = "SELECT id,report_text,name as report_name,total_amount,really_amount,evaluation_aim,purpose,report_final_amount,seal_final_amount FROM `oa_report` WHERE status > 4 AND DATE_FORMAT(`created_at`, '%Y-%m')='" . $data['t'] . "' AND FIND_IN_SET(" . $v . ",partake_uid)";
            $res = Db::table("s")->doSql($sql);
            foreach ($res as $k => $v1) {

                $u = Db::name("user")
                    ->field('nickname')
                    ->where('uid', $v)
                    ->find();
                $res[$k]['nickname'] = $u['nickname'] ?? "";

                $verify = Db::name("report_examine")
                    ->field('u.nickname')
                    ->alias('a')
                    ->join("oa_user u", "a.examine_uid=u.uid", "left")
                    ->where('report_id', $v1['id'])
                    ->where('status', 1)
                    ->where('type', 1)
                    ->find();
                $res[$k]['verify_nickname'] = $verify['nickname'];
                $arr[$k1]['info'] = $res;
                $arr[$k1]['really_amount'] += $v1['really_amount'];
                $arr[$k1]['report_final_amount'] += $v1['report_final_amount'];
                $arr[$k1]['seal_final_amount'] += $v1['seal_final_amount'];
            }


        }
        Json::msg(200, 'success', $arr);

    }

    public function export()
    {
        $data = $this->data;

        $ui = explode(',', $data['uid']);

        $arr = [];

        foreach ($ui as $k1 => $v) {
            $really_amount = 0;
            $report_final_amount = 0;
            $seal_final_amount = 0;
//            $arr[$k1]['nickname'] = $u['nickname'];
            $sql = "SELECT id,report_text,name as report_name,total_amount,really_amount,evaluation_aim,purpose,report_final_amount,seal_final_amount FROM `oa_report` WHERE status > 4 AND DATE_FORMAT(`created_at`, '%Y-%m')='" . $data['t'] . "' AND FIND_IN_SET(" . $v . ",partake_uid)";
            $res = Db::table("s")->doSql($sql);
            foreach ($res as $k => $v1) {

                $u = Db::name("user")
                    ->field('nickname')
                    ->where('uid', $v)
                    ->find();
                $res[$k]['nickname'] = $u['nickname'] ?? "";

                $verify = Db::name("report_examine")
                    ->field('u.nickname')
                    ->alias('a')
                    ->join("oa_user u", "a.examine_uid=u.uid", "left")
                    ->where('report_id', $v1['id'])
                    ->where('status', 1)
                    ->where('type', 1)
                    ->find();
                $res[$k]['verify_nickname'] = $verify['nickname'];
                $arr[$k1]['info'] = $res;
                $arr[$k1]['really_amount'] += $v1['really_amount'];
                $arr[$k1]['report_final_amount'] += $v1['report_final_amount'];
                $arr[$k1]['seal_final_amount'] += $v1['seal_final_amount'];
            }


        }

        $newExcel = new Spreadsheet();  //创建一个新的excel文档
        $objSheet = $newExcel->getActiveSheet();  //获取当前操作sheet的对象
        $objSheet->setTitle($data['t'] . '工资');  //设置当前sheet的标题

        //设置宽度为true,不然太窄了
        $newExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);

        //设置第一栏的标题
//        $objSheet->setCellValue('A1', '姓名')
//            ->setCellValue('B1', '报告编号')
//            ->setCellValue('C1', '报告名称')
//            ->setCellValue('D1', '评价估值')
//            ->setCellValue('E1', '实收费（元）')
//            ->setCellValue('F1', '评估目的')
//            ->setCellValue('G1', '设定用途')
//            ->setCellValue('H1', '审核报告人')
//            ->setCellValue('I1', '报告费')
//            ->setCellValue('J1', '审核费');

        //第二行起，每一行的值,setCellValueExplicit是用来导出文本格式的。
        //->setCellValueExplicit('C' . $k, $val['admin_password']PHPExcel_Cell_DataType::TYPE_STRING),可以用来导出数字不变格式
        $num = 1;
        $key = 0;
        foreach ($arr as $k => $v) {
            $key += 1;
            $objSheet->setCellValue('A' . $key, '姓名')
                ->setCellValue('B' . $key, '报告编号')
                ->setCellValue('C' . $key, '报告名称')
                ->setCellValue('D' . $key, '评价估值')
                ->setCellValue('E' . $key , '实收费（元）')
                ->setCellValue('F' . $key, '评估目的')
                ->setCellValue('G' . $key, '设定用途')
                ->setCellValue('H' . $key, '审核报告人')
                ->setCellValue('I' . $key, '报告费')
                ->setCellValue('J' . $key, '审核费');

            foreach ($v['info'] as $val) {
                $key += 1;
                $objSheet->setCellValue('A' . $key, $val['nickname'])
                    ->setCellValue('B' . $key, $val['report_text'])
                    ->setCellValue('C' . $key, $val['report_name'])
                    ->setCellValue('D' . $key, $val['total_amount'])
                    ->setCellValue('E' . $key, $val['really_amount'])
                    ->setCellValue('F' . $key, $val['evaluation_aim'])
                    ->setCellValue('G' . $key, $val['purpose'])
                    ->setCellValue('H' . $key, $val['report_final_amount'])
                    ->setCellValue('I' . $key, $val['seal_final_amount'])
                    ->setCellValue('J' . $key, $val['verify_nickname']);
            }
//            $key += $num + 1;

            $key += 2;
            $objSheet->setCellValue('A' . $key, '总计：')
                ->setCellValue('B' . $key, '实收费：' . $v['really_amount'] . "元")
                ->setCellValue('C' . $key, '报告费：' . $v['report_final_amount'] . "元")
                ->setCellValue('D' . $key, '审核费：' . $v['seal_final_amount'] . "元");
            $key += 2;

        }
//        echo $num . "\n";
//        echo $key;
        $dz = 'xlsx/jx/' . $data['t'] . '_绩效_' . time() . '.Xls';
        $objWriter = IOFactory::createWriter($newExcel, 'Xls');
        file_put_contents(PUB . $dz, '');
        $objWriter->save(PUB . $dz);
        Json::msg(200, 'success', ['url' => URL . '/' . $dz]);
    }

}