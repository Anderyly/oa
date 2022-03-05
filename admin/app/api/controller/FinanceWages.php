<?php
/**
 * 财务 工资
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

class FinanceWages extends Common
{

    private string $table = "oa_tools_use";
    private string $field = 'id';

    /**
     * 计算绩效
     * @return void
     */
    public function calculatePerformance()
    {
        $data = $this->data;

        $user = Db::name("user")
            ->field("uid,nickname")
            ->where('status', 1)
            ->select();
        $arr = [];
        foreach ($user as $k => $v) {
            $num = Db::table("oa_project_achievements_info")
                ->field("a.all_work")
                ->alias("a")
                ->join("oa_project_achievements b", "a.aid=b.id", "left")
                ->where('a.uid', $v['uid'])
                ->where("DATE_FORMAT(`created_at`, '%Y-%m')", $data['t'])
                ->sum();
            $sql = "SELECT SUM(report_final_amount) AS report_final_amount FROM `oa_report` WHERE DATE_FORMAT(`created_at`, '%Y-%m')='" . $data['t'] . "' AND FIND_IN_SET(" . $v['uid'] . ",partake_uid)";
            $report = Db::table("s")->doSql($sql);

            $arr[$k] = [
                'uid' => $v['uid'],
                'nickname' => $v['nickname'],
                'achievements' => $num,
                'report' => $report[0]['report_final_amount']
            ];

        }

        Json::msg(200, 'success', $arr);

    }

    /**
     * 计算报告
     * @return void
     */
//    public function calculateReport()
//    {
//        $data = $this->data;
//
//
//        Json::msg(200, 'success', ['amount' => $res[0]['report_final_amount']]);
//
//    }


    public function add()
    {
        $data = $_POST;

        $checkArr = [
            'data' => 'require',
        ];
        Validator::check($data, $checkArr);

        $json = json_decode($data['data'], true);
        $insertArr = [];
        foreach ($json['wages'] as $v) {
            $arr = [
                'basic' => $v['basic'],
                'station' => $v['station'],
                'toeic' => $v['toeic'],
                'subsidy' => $v['subsidy'],
                'achievements' => $v['achievements'],
                'report' => $v['report'],
                'meals' => $v['meals'],
                'communication' => $v['communication'],
                'labar' => $v['labar'],
                'enterprise_aged' => $v['enterprise_aged'],
                'personal_aged' => $v['personal_aged'],
                'enterprise_housing' => $v['enterprise_housing'],
                'personal_housing' => $v['personal_housing'],
                'cooling' => $v['cooling'],
            ];

            $arr['total'] = 0;
            foreach ($arr as $v1) {
                $arr['total'] += (int)($v1);
            }
            $arr['real_hair'] = (int)($arr['total']) - (int)($arr['enterprise_aged']) - (int)($arr['personal_aged']) - (int)($arr['enterprise_housing']) - (int)($arr['personal_housing']);
            $arr['y'] = $json['y'];
            $arr['m'] = $json['m'];
            $arr['created_at'] = date('Y-m-d H:i:s', time());
            $arr['uid'] = $v['uid'];
            $insertArr[] = $arr;
        }

        $res = Db::name("wages")->insertAll($insertArr);


        $this->rJson($res);

    }

    public function detail()
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
        Json::msg(200, 'success', $res);

    }

    public function getAllUser()
    {
        $res = Db::name("user")
            ->field("uid as value, nickname as title")
            ->where('status', 1)
            ->select();

        Json::msg(200, 'success', $res);
    }

    public function getAllUser1()
    {
        $res = Db::name("user")
            ->field("uid as value, nickname as title")
            ->where('status', 1)
            ->select();

        Json::msg(200, 'success', $res);
    }

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

        $objWriter = IOFactory::createWriter($newExcel, 'Xls');
        file_put_contents(PUB . 'xlsx/gz/' . $data['t'] . '工资.Xls', '');
        $objWriter->save(PUB . 'xlsx/gz/' . $data['t'] . '工资.Xls');
        Json::msg(200, 'success', ['url' => URL . '/xlsx/gz/' . $data['t'] . '工资.Xls']);
    }


}