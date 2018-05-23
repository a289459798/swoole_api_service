<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:28
 */

namespace Bijou\Example;


use Bijou\Controller;
use Bijou\Example\AsyncTask\ExportApi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class Export extends Controller
{

    /**
     * @return string
     * @Ignore
     */
    public function getApi()
    {

        $this->addAsyncTask(new ExportApiTask(new ExportApi(), $this->getApp()->getRoutes()));
        return "接口正在导出，请查看文件";
    }

    public function phone($count)
    {

//        $spreadsheet = new Spreadsheet();
//
//        $sheet = $spreadsheet->getActiveSheet();
//        $sheet->setCellValue("A1", "姓");
//        $sheet->setCellValue("B1", "名");
//        $sheet->setCellValue("C1", "昵称");
//        $sheet->setCellValue("D1", "QQ号");
//        $sheet->setCellValue("E1", "家庭手机");
//        $sheet->setCellValue("F1", "工作手机");
//        $sheet->setCellValue("G1", "其他手机");
//        $sheet->setCellValue("H1", "家庭电话");
//        $sheet->setCellValue("I1", "工作电话");
//        $sheet->setCellValue("J1", "其他电话");
//        $sheet->setCellValue("K1", "家庭传真");
//        $sheet->setCellValue("L1", "工作传真");
//        $sheet->setCellValue("M1", "公司/部门");
//        $sheet->setCellValue("N1", "家庭地址");
//        $sheet->setCellValue("O1", "工作地址");
//        $sheet->setCellValue("P1", "其他地址");
//        $sheet->setCellValue("Q1", "备注");
//        $sheet->setCellValue("R1", "电子邮件");
//        $sheet->setCellValue("S1", "家庭邮箱");
//        $sheet->setCellValue("T1", "办公邮箱");
//        $sheet->setCellValue("U1", "网址");
//        $sheet->setCellValue("v1", "家庭网址");
//        $sheet->setCellValue("W1", "办公网址");
//        $sheet->setCellValue("X1", "生日");
//        $sheet->setCellValue("Y1", "职务");
//
        $mysql = new \MysqliDb("192.168.1.104", "root", "root", "dianping", "3306");
//
        $data = $mysql->get('dian');
//        foreach ($data as $k => $v) {
//
//            $arr = explode(",", $v['tel']);
//
//            $sheet->setCellValue("E" . ($k + 2), $arr[0]);
//            if(isset($arr[1])) {
//                $sheet->setCellValue("F" . ($k + 2), $arr[1]);
//            }
//
//            $sheet->setCellValue("B" . ($k + 2), $v['name']);
//
//            $sheet->setCellValue("A" . ($k + 2), $v['categoryName']);
//            $sheet->setCellValue("S" . ($k + 2), "12345@qq.com");
//            $sheet->setCellValue("N" . ($k + 2), $v['regionName']);
//
//            if($k == $count) {
//                break;
//            }
//        }
//
//        $write = new Xls($spreadsheet);
//        $write->save("./dianping.xls");

        file_put_contents("./dianping.json", json_encode($data));
    }

}