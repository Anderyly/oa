<?php
/**
 * 汇总
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

class Summary extends Common
{

    /**
     * 工具使用
     * @return void
     * @throws Exception
     */
    public function tools()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['type']) and !empty($data['type'])) $where[] = ["d.type", '=', $data['type']];
        if (isset($data['uid']) and !empty($data['uid'])) $where[] = ["d.uid", '=', $data['uid']];
        if (isset($data['use_at']) and !empty($data['use_at'])) {
            $use_at = $data['use_at'];
            $y = date("Y-m-d H:i:s", strtotime($use_at . " 00:00:00") + 60 * 60 * 24);
            $where[] = ["d.use_at", '>=', $use_at . " 00:00:00"];
            $where[] = ["d.use_at", '<', $y];
        }

        $where[] = ["d.id", '!=', 0];

        $row = Db::table("oa_tools_use")
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

            $approval = Db::table("oa_user")
                ->field('nickname')
                ->where('uid', $v['approval_uid'])
                ->find();
            $row[$k]['approval_nickname'] = $approval['nickname'] ?? "";

            $h_approval = Db::table("oa_user")
                ->field('nickname')
                ->where('uid', $v['h_approval_uid'])
                ->find();
            $row[$k]['h_approval_nickname'] = $h_approval['nickname'] ?? "";

        }

        $count = Db::table("oa_tools_use")
            ->alias("d")
            ->field('id')
            ->where($where)
            ->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    public function toolsExport()
    {

        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['type']) and !empty($data['type'])) $where[] = ["d.type", '=', $data['type']];
        if (isset($data['uid']) and !empty($data['uid'])) $where[] = ["d.uid", '=', $data['uid']];
        if (isset($data['use_at']) and !empty($data['use_at'])) {
            $use_at = $data['use_at'];
            $y = date("Y-m-d H:i:s", strtotime($use_at . " 00:00:00") + 60 * 60 * 24);
            $where[] = ["d.use_at", '>=', $use_at . " 00:00:00"];
            $where[] = ["d.use_at", '<', $y];
        }

        $where[] = ["d.id", '!=', 0];

        $row = Db::table("oa_tools_use")
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

            $approval = Db::table("oa_user")
                ->field('nickname')
                ->where('uid', $v['approval_uid'])
                ->find();
            $row[$k]['approval_nickname'] = $approval['nickname'] ?? "";

            $h_approval = Db::table("oa_user")
                ->field('nickname')
                ->where('uid', $v['h_approval_uid'])
                ->find();
            $row[$k]['h_approval_nickname'] = $h_approval['nickname'] ?? "";

        }


        $newExcel = new Spreadsheet();  //创建一个新的excel文档
        $objSheet = $newExcel->getActiveSheet();  //获取当前操作sheet的对象
        $objSheet->setTitle('工具');  //设置当前sheet的标题

        //设置宽度为true,不然太窄了
        $newExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

        //设置第一栏的标题
        $objSheet->setCellValue('A1', '借用工具类型')
            ->setCellValue('B1', '用工具人')
            ->setCellValue('C1', '所用工具')
            ->setCellValue('D1', '借用时间')
            ->setCellValue('E1', '借用批准人')
            ->setCellValue('F1', '还回批准人')
            ->setCellValue('G1', '还回批准时间');

        //第二行起，每一行的值,setCellValueExplicit是用来导出文本格式的。
        //->setCellValueExplicit('C' . $k, $val['admin_password']PHPExcel_Cell_DataType::TYPE_STRING),可以用来导出数字不变格式
        foreach ($row as $k => $val) {
            $k = $k + 2;
            if ($val['type'] == 1) {
                $className = "日常用工具";
            } else if ($val['type'] == 2) {
                $className = "项目用工具";
            }
            $objSheet->setCellValue('A' . $k, $className)
                ->setCellValue('B' . $k, $val['nickname'])
                ->setCellValue('C' . $k, $val['toolName'])
                ->setCellValue('D' . $k, $val['use_at'])
                ->setCellValue('E' . $k, $val['approval_nickname'])
                ->setCellValue('F' . $k, $val['h_approval_nickname'])
                ->setCellValue('G' . $k, $val['approval_at']);
        }

        $t = time();
        $path = 'xlsx/tools/工具' . $t . '.Xls';
        $objWriter = IOFactory::createWriter($newExcel, 'Xls');
        file_put_contents(PUB . $path, '');
        $objWriter->save(PUB . $path);
        Json::msg(200, 'success', ['url' => URL . '/' . $path]);
    }

    /**
     * 报销
     * @return void
     * @throws Exception
     */
    public function reimbursement()
    {

        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['uid']) and !empty($data['uid'])) $where[] = ["d.uid", '=', $data['uid']];
        if (isset($data['approval_uid']) and !empty($data['approval_uid'])) $where[] = ["d.approval_uid", '=', $data['approval_uid']];
        if (isset($data['verify_uid']) and !empty($data['verify_uid'])) $where[] = ["d.verify_uid", '=', $data['verify_uid']];
        if (isset($data['type']) and !empty($data['type'])) $where[] = ["d.type", '=', $data['type']];

        $where[] = ["d.id", '!=', 0];

        $row = Db::table("oa_reimbursement")
            ->alias("d")
            ->field("d.*,u.nickname")
            ->join("oa_user u", "d.uid=u.uid", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();

        $count = Db::table("oa_reimbursement")
            ->alias("d")
            ->field('id')
            ->where($where)
            ->count();

        foreach ($row as $k => $v) {
            $approval = Db::table("oa_user")
                ->field('nickname')
                ->where('uid', $v['approval_uid'])
                ->find();
            $row[$k]['approval_nickname'] = $approval['nickname'] ?? "";

            $h_approval = Db::table("oa_user")
                ->field('nickname')
                ->where('uid', $v['verify_uid'])
                ->find();
            $row[$k]['verify_nickname'] = $h_approval['nickname'] ?? "";

        }
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    public function reimbursementExport()
    {

        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['uid']) and !empty($data['uid'])) $where[] = ["d.uid", '=', $data['uid']];
        if (isset($data['approval_uid']) and !empty($data['approval_uid'])) $where[] = ["d.approval_uid", '=', $data['approval_uid']];
        if (isset($data['verify_uid']) and !empty($data['verify_uid'])) $where[] = ["d.verify_uid", '=', $data['verify_uid']];
        if (isset($data['type']) and !empty($data['type'])) $where[] = ["d.type", '=', $data['type']];

        $where[] = ["d.id", '!=', 0];

        $row = Db::table("oa_reimbursement")
            ->alias("d")
            ->field("d.*,u.nickname")
            ->join("oa_user u", "d.uid=u.uid", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();

        foreach ($row as $k => $v) {
            $approval = Db::table("oa_user")
                ->field('nickname')
                ->where('uid', $v['approval_uid'])
                ->find();
            $row[$k]['approval_nickname'] = $approval['nickname'] ?? "";

            $h_approval = Db::table("oa_user")
                ->field('nickname')
                ->where('uid', $v['verify_uid'])
                ->find();
            $row[$k]['verify_nickname'] = $h_approval['nickname'] ?? "";

        }

        $newExcel = new Spreadsheet();  //创建一个新的excel文档
        $objSheet = $newExcel->getActiveSheet();  //获取当前操作sheet的对象
        $objSheet->setTitle('报销');  //设置当前sheet的标题

        //设置宽度为true,不然太窄了
        $newExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);

        //设置第一栏的标题
        $objSheet->setCellValue('A1', '报销类型')
            ->setCellValue('B1', '报销金额')
            ->setCellValue('C1', '实际报销')
            ->setCellValue('D1', '发票结余')
            ->setCellValue('E1', '单据张数')
            ->setCellValue('F1', '报销人')
            ->setCellValue('G1', '报销内容');

        //第二行起，每一行的值,setCellValueExplicit是用来导出文本格式的。
        //->setCellValueExplicit('C' . $k, $val['admin_password']PHPExcel_Cell_DataType::TYPE_STRING),可以用来导出数字不变格式
        foreach ($row as $k => $val) {
            $k = $k + 2;
            if ($val['type'] == 1) {
                $className = "日常报销";
            } else if ($val['type'] == 2) {
                $className = "项目报销";
            } else {
                $className = "找票";
            }
            $objSheet->setCellValue('A' . $k, $className)
                ->setCellValue('B' . $k, $val['amount'])
                ->setCellValue('C' . $k, $val['actual_amount'])
                ->setCellValue('D' . $k, $val['invoice_balance'])
                ->setCellValue('E' . $k, $val['bill'])
                ->setCellValue('F' . $k, $val['nickname'])
                ->setCellValue('G' . $k, $val['content']);
        }

        $t = time();
        $path = 'xlsx/bx/报销' . $t . '.Xls';
        $objWriter = IOFactory::createWriter($newExcel, 'Xls');
        file_put_contents(PUB . $path, '');
        $objWriter->save(PUB . $path);
        Json::msg(200, 'success', ['url' => URL . '/' . $path]);
    }

    /**
     * 请假
     * @return void
     */
    public function leave()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['uid']) and !empty($data['uid'])) $where[] = ["l.uid", '=', $data['uid']];
        if (isset($data['type']) and !empty($data['type'])) $where[] = ["l.type", '=', $data['type']];

        if (isset($data['start_time']) and !empty($dataa['start_time'])) {
            $where[] = ["l.start_time", '>=', strtotime($data['start_time'])];
        }
        if (isset($data['end_time']) and !empty($data['end_time'])) {
            $where[] = ["l.end_time", '<', strtotime($data['end_time'])];
        }

        $where[] = ["l.id", '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['l.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table("oa_leave")
            ->alias("l")
            ->field('l.type,l.meta,l.duration,l.start_time,user.nickname')
            ->join("oa_user user", "l.uid=user.uid", "LEFT")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("l.id desc")
            ->select();

//        foreach ($row as $k => $v) {
//            if (empty($v['operate_uid']) or $v['operate_uid'] == 0) continue;
//            $user = Db::name("user")->field("nickname")->where('uid', $v['operate_uid'])->find();
//            if ($user) {
//                $row[$k]['operate_name'] = $user['nickname'];
//            }
//        }
        $count = Db::table("oa_leave")
            ->alias("l")
            ->field("id")
            ->where($where)
            ->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    public function leaveExport()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['uid']) and !empty($data['uid'])) $where[] = ["l.uid", '=', $data['uid']];
        if (isset($data['type']) and !empty($data['type'])) $where[] = ["l.type", '=', $data['type']];

        if (isset($data['start_time']) and !empty($dataa['start_time'])) {
            $where[] = ["l.start_time", '>=', strtotime($data['start_time'])];
        }
        if (isset($data['end_time']) and !empty($data['end_time'])) {
            $where[] = ["l.end_time", '<', strtotime($data['end_time'])];
        }

        $where[] = ["l.id", '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['l.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::table("oa_leave")
            ->alias("l")
            ->field('l.type,c.name as typeName,l.meta,l.duration,l.start_time,user.nickname')
            ->join("oa_user user", "l.uid=user.uid", "LEFT")
            ->join("oa_leave_class c", "l.type=c.id", "LEFT")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("l.id desc")
            ->select();


        $newExcel = new Spreadsheet();  //创建一个新的excel文档
        $objSheet = $newExcel->getActiveSheet();  //获取当前操作sheet的对象
        $objSheet->setTitle('请假');  //设置当前sheet的标题

        //设置宽度为true,不然太窄了
        $newExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

        //设置第一栏的标题
        $objSheet->setCellValue('A1', '请假类型')
            ->setCellValue('B1', '请假人')
            ->setCellValue('C1', '请假时间')
            ->setCellValue('D1', '请假天数')
            ->setCellValue('E1', '备注');

        //第二行起，每一行的值,setCellValueExplicit是用来导出文本格式的。
        //->setCellValueExplicit('C' . $k, $val['admin_password']PHPExcel_Cell_DataType::TYPE_STRING),可以用来导出数字不变格式
        foreach ($row as $k => $val) {
            $k = $k + 2;

            $objSheet->setCellValue('A' . $k, $val['typeName'])
                ->setCellValue('B' . $k, $val['nickname'])
                ->setCellValue('C' . $k, $val['start_time'])
                ->setCellValue('D' . $k, $val['duration'])
                ->setCellValue('E' . $k, $val['meta']);
        }

        $t = time();
        $path = 'xlsx/leave/请假' . $t . '.Xls';
        $objWriter = IOFactory::createWriter($newExcel, 'Xls');
        file_put_contents(PUB . $path, '');
        $objWriter->save(PUB . $path);
        Json::msg(200, 'success', ['url' => URL . '/' . $path]);
    }

    public function report()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['report_uid']) and !empty($data['report_uid'])) $where[] = ["d.report_uid", '=', $data['report_uid']];
        if (isset($data['uid']) and !empty($data['uid'])) $where[] = ["d.uid", '=', $data['uid']];
        if (isset($data['name']) and !empty($data['name'])) $where[] = ["d.report_name", ' like ', "%" . $data['name'] . "%"];
        if (isset($data['bank']) and !empty($data['bank'])) $where[] = ["d.bank", ' like ', "%" . $data['bank'] . "%"];

        if (isset($data['entrusted_unit']) and !empty($data['entrusted_unit'])) $where[] = ["d.entrusted_unit", ' like ', "%" . $data['entrusted_unit'] . "%"];
        if (isset($data['evaluation_aim']) and !empty($data['evaluation_aim'])) $where[] = ["d.evaluation_aim", ' like ', "%" . $data['evaluation_aim'] . "%"];

        if (isset($data['type']) and !empty($data['type'])) $where[] = ["c.id", '=', $data['type']];

        if (isset($data['start_time']) and !empty($data['start_time'])) {
            $where[] = ["d.created_at", '>=', $data['start_time'] . ' 00:00:00'];
            $where[] = ["d.created_at", '<', $data['start_time'] . ' 23:59:59'];
        }
        if (isset($data['end_time']) and !empty($data['end_time'])) {
            $where[] = ["d.created_at", '<', $data['end_time']];
        }

        $where[] = ["d.id", '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::name("report")
            ->alias("d")
            ->field("d.name,c.name as report_type_name,report_text,benchmark_date,u.nickname as report_nickname,d.report_name,d.uid,d.created_at,d.entrusted_unit,d.name,d.land_size,d.floor_size,d.evaluation_aim,d.evaluation_date,d.purpose,d.bank,d.total_amount,d.seal_final_amount,d.report_final_amount,d.customer,d.id,s.name as seal_quality_name,d.end_before_amount,d.bil_amount,d.really_amount,d.end_uid,d.end_created_at,d.end_meta")
            ->join("oa_user u", "d.report_uid=u.uid", "left")
            ->join("oa_report_type c", "d.report_type=c.type", "left")
            ->join("oa_seal s", "d.seal_quality=s.id", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();

        foreach ($row as $k => $v) {
            $user = Db::name("user")
                ->alias("a")
                ->field("a.nickname,c.name as company_name")
                ->join("oa_department b", "a.unit=b.id", "left")
                ->join("oa_company c", "b.company_id=c.id", "left")
                ->where('a.uid', $v['uid'])
                ->find();
            $row[$k]['nickname'] = $user['nickname'] ?? "";
            $row[$k]['company_name'] = $user['company_name'] ?? "";


            // 审核
            $verify = Db::name("report_examine")
                ->alias('d')
                ->field('u.nickname as verify_nickname,d.examine_date')
                ->join("oa_user u", "d.examine_uid=u.uid", "left")
                ->where('d.type', 1)
                ->where('d.report_id', $v['id'])
                ->order('id desc')
                ->find();
            $row[$k]['verify_examine_date'] = $verify['examine_date'] ?? "";
            $row[$k]['verify_nickname'] = $verify['verify_nickname'] ?? "";
            $row[$k]['verify_count'] = Db::name("report_examine")
                ->where('type', 1)
                ->where('report_id', $v['id'])
                ->field('id')
                ->count();

            // 客户
            $kh = json_decode($v['customer'], true);
            $row[$k]['kh_name'] = '';
            $row[$k]['kh_phone'] = '';
            foreach ($kh as $v1) {
                $row[$k]['kh_name'] .= $v1['kh_name'] . ',';
                $row[$k]['kh_phone'] .= $v1['kh_phone'] . ',';
            }
            $row[$k]['kh_name'] = rtrim($row[$k]['kh_name'], ',');
            $row[$k]['kh_phone'] = rtrim($row[$k]['kh_phone'], ',');

            // 盖章
            $seal = Db::name("report_examine")
                ->alias('d')
                ->field('u.nickname as seal_nickname,d.examine_date')
                ->join("oa_user u", "d.examine_uid=u.uid", "left")
                ->where('d.type', 2)
                ->where('d.report_id', $v['id'])
                ->order('id desc')
                ->find();
            $row[$k]['seal_examine_date'] = $seal['examine_date'] ?? "";
            $row[$k]['seal_nickname'] = $seal['seal_nickname'] ?? "";
            $row[$k]['seal_count'] = Db::name("report_examine")
                ->where('type', 2)
                ->where('report_id', $v['id'])
                ->field('id')
                ->count();

            // 财务
            $user = Db::name("user")->field("nickname")->where('uid', $v['end_uid'])->find();
            $row[$k]['end_nickname'] = $user['nickname'] ?? "";

            unset($row[$k]['uid']);
            unset($row[$k]['end_uid']);
            unset($row[$k]['id']);
        }

//        foreach ($row as $k => $v) {
//            if (empty($v['operate_uid']) or $v['operate_uid'] == 0) continue;
//            $user = Db::name("user")->field("nickname")->where('uid', $v['operate_uid'])->find();
//            if ($user) {
//                $row[$k]['operate_name'] = $user['nickname'];
//            }
//        }
        $count = Db::name("report")
            ->alias("d")
            ->field("d.id")
            ->where($where)
            ->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }
    public function reportExport()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['report_uid']) and !empty($data['report_uid'])) $where[] = ["d.report_uid", '=', $data['report_uid']];
        if (isset($data['uid']) and !empty($data['uid'])) $where[] = ["d.uid", '=', $data['uid']];
        if (isset($data['name']) and !empty($data['name'])) $where[] = ["d.report_name", ' like ', "%" . $data['name'] . "%"];
        if (isset($data['bank']) and !empty($data['bank'])) $where[] = ["d.bank", ' like ', "%" . $data['bank'] . "%"];

        if (isset($data['entrusted_unit']) and !empty($data['entrusted_unit'])) $where[] = ["d.entrusted_unit", ' like ', "%" . $data['entrusted_unit'] . "%"];
        if (isset($data['evaluation_aim']) and !empty($data['evaluation_aim'])) $where[] = ["d.evaluation_aim", ' like ', "%" . $data['evaluation_aim'] . "%"];

        if (isset($data['type']) and !empty($data['type'])) $where[] = ["c.id", '=', $data['type']];

        if (isset($data['start_time']) and !empty($data['start_time'])) {
            $where[] = ["d.created_at", '>=', $data['start_time']];
        }
        if (isset($data['end_time']) and !empty($data['end_time'])) {
            $where[] = ["d.created_at", '<', $data['end_time']];
        }

        $where[] = ["d.id", '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::name("report")
            ->alias("d")
            ->field("d.name,c.name as report_type_name,report_text,benchmark_date,u.nickname as report_nickname,d.report_name,d.uid,d.created_at,d.entrusted_unit,d.name,d.land_size,d.floor_size,d.evaluation_aim,d.evaluation_date,d.purpose,d.bank,d.total_amount,d.seal_final_amount,d.report_final_amount,d.customer,d.id,s.name as seal_quality_name,d.end_before_amount,d.bil_amount,d.really_amount,d.end_uid,d.end_created_at,d.end_meta")
            ->join("oa_user u", "d.report_uid=u.uid", "left")
            ->join("oa_report_type c", "d.report_type=c.type", "left")
            ->join("oa_seal s", "d.seal_quality=s.id", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();

        foreach ($row as $k => $v) {
            $user = Db::name("user")
                ->alias("a")
                ->field("a.nickname,c.name as company_name")
                ->join("oa_department b", "a.unit=b.id", "left")
                ->join("oa_company c", "b.company_id=c.id", "left")
                ->where('a.uid', $v['uid'])
                ->find();
            $row[$k]['nickname'] = $user['nickname'] ?? "";
            $row[$k]['company_name'] = $user['company_name'] ?? "";


            // 审核
            $verify = Db::name("report_examine")
                ->alias('d')
                ->field('u.nickname as verify_nickname,d.examine_date')
                ->join("oa_user u", "d.examine_uid=u.uid", "left")
                ->where('d.type', 1)
                ->where('d.report_id', $v['id'])
                ->order('id desc')
                ->find();
            $row[$k]['verify_examine_date'] = $verify['examine_date'] ?? "";
            $row[$k]['verify_nickname'] = $verify['verify_nickname'] ?? "";
            $row[$k]['verify_count'] = Db::name("report_examine")
                ->where('type', 1)
                ->where('report_id', $v['id'])
                ->field('id')
                ->count();

            // 客户
            $kh = explode(',', $v['customer']);
            $row[$k]['kh_name'] = '';
            $row[$k]['kh_phone'] = '';
            foreach ($kh as $v1) {
                $row[$k]['kh_name'] .= $v1['kh_name'] . ',';
                $row[$k]['kh_phone'] .= $v1['kh_phone'] . ',';
            }
            $row[$k]['kh_name'] = rtrim($row[$k]['kh_name'], ',');
            $row[$k]['kh_phone'] = rtrim($row[$k]['kh_phone'], ',');

            // 盖章
            $seal = Db::name("report_examine")
                ->alias('d')
                ->field('u.nickname as seal_nickname,d.examine_date')
                ->join("oa_user u", "d.examine_uid=u.uid", "left")
                ->where('d.type', 2)
                ->where('d.report_id', $v['id'])
                ->order('id desc')
                ->find();
            $row[$k]['seal_examine_date'] = $seal['examine_date'] ?? "";
            $row[$k]['seal_nickname'] = $seal['seal_nickname'] ?? "";
            $row[$k]['seal_count'] = Db::name("report_examine")
                ->where('type', 2)
                ->where('report_id', $v['id'])
                ->field('id')
                ->count();

            // 财务
            $user = Db::name("user")->field("nickname")->where('uid', $v['end_uid'])->find();
            $row[$k]['end_nickname'] = $user['nickname'] ?? "";

            unset($row[$k]['uid']);
            unset($row[$k]['end_uid']);
            unset($row[$k]['id']);
        }

        $newExcel = new Spreadsheet();  //创建一个新的excel文档
        $objSheet = $newExcel->getActiveSheet();  //获取当前操作sheet的对象
        $objSheet->setTitle($data['t'] . '工资');  //设置当前sheet的标题

        //设置宽度为true,不然太窄了
        $newExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('AA1')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('AB1')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('AC1')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('AD1')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('AE1')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('AF1')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('AG1')->setAutoSize(true);

        //设置第一栏的标题
        $objSheet->setCellValue('A1', '报告类型')
            ->setCellValue('B1', '报告编号')
            ->setCellValue('C1', '估价基准日')
            ->setCellValue('D1', '项目名称')
            ->setCellValue('E1', '作报告人')
            ->setCellValue('F1', '所属公司')
            ->setCellValue('G1', '发起人')
            ->setCellValue('H1', '发起时间')
            ->setCellValue('I1', '委托单位')
            ->setCellValue('J1', '报告名称')
            ->setCellValue('K1', '土地面积（㎡）')
            ->setCellValue('L1', '建筑面积（㎡）')
            ->setCellValue('M1', '估价目的')
            ->setCellValue('N1', '估价时点')
            ->setCellValue('O1', '设定用途')
            ->setCellValue('P1', '使用银行')
            ->setCellValue('Q1', '房地产总价（万元）')
            ->setCellValue('R1', '联系人')
            ->setCellValue('S1', '联系电话')
            ->setCellValue('T1', '审核人')
            ->setCellValue('U1', '审核时间')
            ->setCellValue('V1', '审核次数')
            ->setCellValue('W1', '审核质量')
            ->setCellValue('X1', '报告费用')
            ->setCellValue('Y1', '盖章人')
            ->setCellValue('Z1', '盖章时间')
            ->setCellValue('AA1', '审核费用')
            ->setCellValue('AB1', '收款人')
            ->setCellValue('AC1', '预收款（元）')
            ->setCellValue('AD1', '实收款（元）')
            ->setCellValue('AE1', '开票金额（元）')
            ->setCellValue('AF1', '收费日期')
            ->setCellValue('AG1', '备注');

        //第二行起，每一行的值,setCellValueExplicit是用来导出文本格式的。
        //->setCellValueExplicit('C' . $k, $val['admin_password']PHPExcel_Cell_DataType::TYPE_STRING),可以用来导出数字不变格式
        foreach ($row as $k => $val) {
            $k = $k + 2;
            $objSheet->setCellValue('A' . $k, $val['report_type_name'])
                ->setCellValue('B' . $k, $val['report_text'])
                ->setCellValue('C' . $k, $val['benchmark_date'])
                ->setCellValue('D' . $k, $val['name'])
                ->setCellValue('E' . $k, $val['report_nickname'])
                ->setCellValue('F' . $k, $val['company_name'])
                ->setCellValue('G' . $k, $val['nickname'])
                ->setCellValue('H' . $k, $val['created_at'])
                ->setCellValue('I' . $k, $val['entrusted_unit'])
                ->setCellValue('J' . $k, $val['report_name'])
                ->setCellValue('K' . $k, $val['land_size'])
                ->setCellValue('L' . $k, $val['floor_size'])
                ->setCellValue('M' . $k, $val['evaluation_aim'])
                ->setCellValue('N' . $k, $val['evaluation_date'])
                ->setCellValue('O' . $k, $val['purpose'])
                ->setCellValue('P' . $k, $val['bank'])
                ->setCellValue('Q' . $k, $val['total_amount'])
                ->setCellValue('R' . $k, $val['kh_name'])
                ->setCellValue('S' . $k, $val['kh_phone'])
                ->setCellValue('T' . $k, $val['verify_name'])
                ->setCellValue('U' . $k, $val['verify_examine_date'])
                ->setCellValue('V' . $k, $val['verify_count'])
                ->setCellValue('W' . $k, $val['seal_quality_name'])
                ->setCellValue('X' . $k, $val['report_final_amount'])
                ->setCellValue('Y' . $k, $val['seal_nickname'])
                ->setCellValue('Z' . $k, $val['seal_examine_date'])
                ->setCellValue('AA' . $k, $val['seal_final_amount'])
                ->setCellValue('AB' . $k, $val['end_nickname'])
                ->setCellValue('AC' . $k, $val['end_before_amount'])
                ->setCellValue('AD' . $k, $val['really_amount'])
                ->setCellValue('AE' . $k, $val['bil_amount'])
                ->setCellValue('AF' . $k, $val['end_created_at'])
                ->setCellValue('AG' . $k, $val['end_meta']);
        }

        $t = time();
        $objWriter = IOFactory::createWriter($newExcel, 'Xls');
        file_put_contents(PUB . 'xlsx/report/报告' . $t . '.Xls', '');
        $objWriter->save(PUB . 'xlsx/report/报告' . $t . '.Xls');
        Json::msg(200, 'success', ['url' => URL . '/xlsx/report/报告' . $t . '.Xls']);
    }

    public function project()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['manager']) and !empty($data['manager'])) $where[] = ["d.manager", '=', $data['manager']];

        if (isset($data['partner']) and !empty($data['partner'])) $where[] = ["c.partner", ' like ', '%' . $data['partner'] . '%'];
        if (isset($data['client']) and !empty($data['client'])) $where[] = ["d.client", ' like ', '%' . $data['client'] . '%'];

        if (isset($data['start_time']) and !empty($data['start_time'])) {
            $where[] = ["d.created_at", '>=', $data['start_time']];
        }
        if (isset($data['end_time']) and !empty($data['end_time'])) {
            $where[] = ["d.created_at", '<', $data['end_time']];
        }

        $where[] = ["d.id", '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::name("project")
            ->alias("d")
            ->field("c.partake_uid,d.id,d.name,d.client,d.contacts,c.partner,d.phone,u.nickname as manager_nickname,d.really_amount,d.bil_amount,d.created_at,d.end_created_at")
            ->join("oa_user u", "d.manager=u.uid", "left")
            ->join("oa_project_info c", "d.id=c.project_id", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();

        foreach ($row as $k => $v) {
            $row[$k]['partake'] = empty($v['partake_uid']) ? 0 : count(explode(',', $v['partake_uid']));
            // TODO 项目成本（元） 具体不清楚是否是报销金额
            $row[$k]['cost'] = Db::name("reimbursement")
                ->field('amount')
                ->where('type', 2)
                ->where('status', 5)
                ->where('project_id', $v['id'])
                ->sum();

            $wages = 0;
            $row[$k]['achievements'] = Db::name("project_achievements")
                ->alias('a')
                ->field("a.total_all_work")
                ->join("oa_project_achievements_verify v", "a.id=v.ach_id", "left")
                ->where("a.project_id", $v['id'])
                ->where('v.status', 1)
                ->sum();
            $row[$k]['profit'] = (int)($v['really_amount']) - (int)($row[$k]['cost']) - (int)($row[$k]['achievements']);
        }
        $count = Db::name("project")
            ->alias("d")
            ->field("d.id")
            ->where($where)
            ->count();
        Json::msg(200, '获取成功', $row, ['count' => $count]);

    }

    public function projectExport()
    {
        $data = $this->data;
        if (!isset($data['rows']) or empty($data['rows'])) $data['rows'] = 10;
        if (!isset($data['page']) or empty($data['page'])) $data['page'] = 1;

        if (isset($data['manager']) and !empty($data['manager'])) $where[] = ["d.manager", '=', $data['manager']];

        if (isset($data['partner']) and !empty($data['partner'])) $where[] = ["c.partner", ' like ', '%' . $data['partner'] . '%'];

        if (isset($data['start_time']) and !empty($data['start_time'])) {
            $where[] = ["d.created_at", '>=', $data['start_time']];
        }
        if (isset($data['end_time']) and !empty($data['end_time'])) {
            $where[] = ["d.created_at", '<', $data['end_time']];
        }

        $where[] = ["d.id", '!=', 0];
        if (isset($data['key']) and !empty($data['key'])) {
            $where[] = ['d.name', 'LIKE', '%' . $data['key'] . '%'];
        }
        $row = Db::name("project")
            ->alias("d")
            ->field("c.partake_uid,d.id,d.name,d.client,d.contacts,c.partner,d.phone,u.nickname as manager_nickname,d.really_amount,d.bil_amount,d.created_at,d.end_created_at")
            ->join("oa_user u", "d.manager=u.uid", "left")
            ->join("oa_project_info c", "d.id=c.project_id", "left")
            ->where($where)
            ->limit($data['page'], $data['rows'])
            ->order("d.id desc")
            ->select();

        foreach ($row as $k => $v) {
            $row[$k]['partake'] = empty($v['partake_uid']) ? 0 : count(explode(',', $v['partake_uid']));
            // TODO 项目成本（元） 具体不清楚是否是报销金额
            $row[$k]['cost'] = Db::name("reimbursement")
                ->field('amount')
                ->where('type', 2)
                ->where('status', 5)
                ->where('project_id', $v['id'])
                ->sum();

            $wages = 0;
            $row[$k]['achievements'] = Db::name("project_achievements")
                ->alias('a')
                ->field("a.total_all_work")
                ->join("oa_project_achievements_verify v", "a.id=v.ach_id", "left")
                ->where("a.project_id", $v['id'])
                ->where('v.status', 1)
                ->sum();
            $row[$k]['profit'] = (int)($v['really_amount']) - (int)($row[$k]['cost']) - (int)($row[$k]['achievements']);
        }

        $newExcel = new Spreadsheet();  //创建一个新的excel文档
        $objSheet = $newExcel->getActiveSheet();  //获取当前操作sheet的对象
        $objSheet->setTitle($data['t'] . '工资');  //设置当前sheet的标题

        //设置宽度为true,不然太窄了
        $newExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $newExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

        //设置第一栏的标题
        $objSheet->setCellValue('A1', '项目名称')
            ->setCellValue('B1', '项目经理')
            ->setCellValue('C1', '委托方')
            ->setCellValue('D1', '投资主体')
            ->setCellValue('E1', '实施主体')
            ->setCellValue('F1', '收费总额（元）')
            ->setCellValue('G1', '开票总额（元）')
            ->setCellValue('H1', '参与人员（名）')
            ->setCellValue('I1', '项目成本（元）')
            ->setCellValue('J1', '项目工资总额（元）')
            ->setCellValue('K1', '项目利润（元）')
            ->setCellValue('L1', '发起日期')
            ->setCellValue('M1', '结束日期');

        //第二行起，每一行的值,setCellValueExplicit是用来导出文本格式的。
        //->setCellValueExplicit('C' . $k, $val['admin_password']PHPExcel_Cell_DataType::TYPE_STRING),可以用来导出数字不变格式
        foreach ($row as $k => $val) {
            $k = $k + 2;
            $objSheet->setCellValue('A' . $k, $val['name'])
                ->setCellValue('B' . $k, $val['manager_nickname'])
                ->setCellValue('C' . $k, $val['client'])
                ->setCellValue('D' . $k, $val['partner'])
                ->setCellValue('E' . $k, $val['manager_nickname'])
                ->setCellValue('F' . $k, $val['really_amount'])
                ->setCellValue('G' . $k, $val['bil_amount'])
                ->setCellValue('H' . $k, $val['partake'])
                ->setCellValue('I' . $k, $val['cost'])
                ->setCellValue('J' . $k, $val['achievements'])
                ->setCellValue('K' . $k, $val['profit'])
                ->setCellValue('L' . $k, $val['created_at'])
                ->setCellValue('M' . $k, $val['end_created_at']);
        }

        $t = time();
        $objWriter = IOFactory::createWriter($newExcel, 'Xls');
        file_put_contents(PUB . 'xlsx/project/项目' . $t . '.Xls', '');
        $objWriter->save(PUB . 'xlsx/project/项目' . $t . '.Xls');
        Json::msg(200, 'success', ['url' => URL . '/xlsx/project/项目' . $t . '.Xls']);
    }


}