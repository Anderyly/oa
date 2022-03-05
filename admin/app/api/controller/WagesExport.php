<?php

namespace app\api\controller;

use ay\lib\Db;
use ay\lib\Json;
use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\IOFactory;//use \PHPExcel_Style_NumberFormat;    //设置列的格式==>>设置文本格式

class WagesExport extends Common
{
    public function export()
    {
        $data = $this->data;

        $ui = explode(',', $data['uid']);
        $str = '';
        foreach ($ui as $v) {
            $str .= "DATE_FORMAT(`created_at`, '%Y-%m')='" . $data['t'] . "' AND uid=" . $v . " OR ";
        }
        $str = rtrim($str, ' OR ');


        $sql = "SELECT * FROM `oa_wages` WHERE $str";
        $res = Db::table("s")->doSql($sql);
        foreach ($res as $k => $v) {
            $u = Db::name("user")
                ->field('nickname')
                ->where('uid', $v['uid'])
                ->find();
            $res[$k]['nickname'] = $u['nickname'] ?? "";

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
        $objSheet->setCellValue('A1', '姓名')
            ->setCellValue('B1', '基本工资')
            ->setCellValue('C1', '岗位工资')
            ->setCellValue('D1', '托业')
            ->setCellValue('E1', '外业补助')
            ->setCellValue('F1', '项目绩效')
            ->setCellValue('G1', '报告费')
            ->setCellValue('H1', '餐费')
            ->setCellValue('I1', '通讯费')
            ->setCellValue('J1', '劳保')
            ->setCellValue('K1', '降温费')
            ->setCellValue('L1', '养老企业缴纳')
            ->setCellValue('M1', '养老个人缴纳')
            ->setCellValue('N1', '公积金企业缴纳')
            ->setCellValue('O1', '公积金个人缴纳')
            ->setCellValue('P1', '工资合计')
            ->setCellValue('Q1', '实发工资');

        //第二行起，每一行的值,setCellValueExplicit是用来导出文本格式的。
        //->setCellValueExplicit('C' . $k, $val['admin_password']PHPExcel_Cell_DataType::TYPE_STRING),可以用来导出数字不变格式
        foreach ($res as $k => $val) {
            $k = $k + 2;
            $objSheet->setCellValue('A' . $k, $val['nickname'])
                ->setCellValue('B' . $k, $val['basic'])
                ->setCellValue('C' . $k, $val['station'])
                ->setCellValue('D' . $k, $val['toeic'])
                ->setCellValue('E' . $k, $val['subsidy'])
                ->setCellValue('F' . $k, $val['achievements'])
                ->setCellValue('G' . $k, $val['report'])
                ->setCellValue('H' . $k, $val['meals'])
                ->setCellValue('I' . $k, $val['communication'])
                ->setCellValue('J' . $k, $val['labar'])
                ->setCellValue('K' . $k, $val['cooling'])
                ->setCellValue('L' . $k, $val['enterprise_aged'])
                ->setCellValue('M' . $k, $val['personal_aged'])
                ->setCellValue('N' . $k, $val['enterprise_housing'])
                ->setCellValue('O' . $k, $val['personal_housing'])
                ->setCellValue('P' . $k, $val['total'])
                ->setCellValue('Q' . $k, $val['real_hair']);
        }

        $this->downloadExcel($newExcel, $data['t'] . '工资', 'Xls');
    }

    //公共文件，用来传入xls并下载
    public function downloadExcel($newExcel, $filename, $format)
    {
        // $format只能为 Xlsx 或 Xls
//        if ($format == 'Xlsx') {
//            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//        } elseif ($format == 'Xls') {
//            header('Content-Type: application/vnd.ms-excel');
//        }

//        header("Content-Disposition: attachment;filename=" . $filename . '.' . strtolower($format));
//        header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($newExcel, $format);

//        $objWriter->save('php://output');

        //通过php保存在本地的时候需要用到
//        echo URL . '/xlsx/gz/' . $filename . '.xlsx';exit;
        file_put_contents(PUB . 'xlsx/gz/' . $filename . '.' . $format, '');
        $objWriter->save(PUB . 'xlsx/gz/' . $filename . '.' . $format);

        Json::msg(200, 'success', ['url' => URL . '/xlsx/gz/' . $filename . '.' . $format]);
        //以下为需要用到IE时候设置
        // If you're serving to IE 9, then the following may be needed
        //header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        //header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        //header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        //header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        //header('Pragma: public'); // HTTP/1.0
        exit;
    }
}