<?php
/**
 * notes: Excel 处理类
 * @author 陈鸿扬 | @date 2021/3/12 18:14
 */

namespace Extend\Util;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExcelTable
{

    /*
     * notes: 根据数组上标合成 excel 列标识
     * @author 陈鸿扬 | @date 2021/3/8 18:01
     */
    protected static function getCellKey($num=0){
        $num = $num+1; //匹配上标,从0开始
        $keyBaseNum = 64; //64="@"
        $cellNum = $keyBaseNum+$num; //当前字符整数
        if ( $cellNum > 90 ) { //90="Z"
            $integer = intval($num/26); $remainder = ($num%26);
            $cellKey = chr($keyBaseNum+$integer).chr($keyBaseNum+$remainder);//超过26个字母 AA,AB,AC,AD...BA,BB...
        }else{
            $cellKey = chr( $cellNum );
        }
        return $cellKey;
    }

    /*
     * notes: 列表数据转Excel
     * @author 陈鸿扬 | @date 2021/3/8 16:15
     */
    public static function listDataExport($data,$tableHeader,$fileName,$format = 'xlsx', $tableSheet='sheet'){

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("Admin")    //作者
            ->setLastModifiedBy("Admin") //最后修改者
            ->setTitle("Office 2007 XLSX Document")  //标题
            ->setSubject("Office 2007 XLSX Document") //副标题
            ->setDescription("document for Office 2007 XLSX, generated using PHP classes.")  //描述
            ->setKeywords("office 2007 openxml php") //关键字
            ->setCategory("file"); //分类

        //居中设置
        $styleArray = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        //设置当前工作表标题
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle( $tableSheet );
        //$worksheet->getDefaultColumnDimension()->setWidth(15);
        //$worksheet->getDefaultRowDimension()->setRowHeight(20);


        //设置中文表头
        $row = 1; //第一行
        $i=0; //列递进
        foreach ($tableHeader as $key=>$name){
            $col = self::getCellKey($i); // 根据数组上标合成 excel 列标识

            $worksheet->getStyle( $col.$row )->getFont()->setBold(true);
            $worksheet->getStyle( $col.$row )->getAlignment()->setWrapText(true);
            $worksheet->getStyle( $col.$row )->applyFromArray($styleArray);
            $worksheet->getColumnDimension( $col )->setAutoSize(true);
            $worksheet->setCellValue( $col.$row, $tableHeader[$key] );

            $i++;
        }

        //填充行
        foreach ($data as $index => $arr) { $row++;
            $j=0;
            foreach ($tableHeader as $key=>$name){
                if( isset($arr[$key]) ){
                    $col = self::getCellKey($j); // 根据数组上标合成 excel 列标识

                    $worksheet->getStyle( $col.$row )->getAlignment()->setWrapText(true);
                    $worksheet->getStyle( $col.$row )->applyFromArray($styleArray);
                    $worksheet->getColumnDimension( $col )->setAutoSize(true);
                    $worksheet->setCellValue( $col.$row, $arr[$key] );

                    $j++;
                }
            }
        }

        //第一种保存方式
        //$writer = new Xlsx($spreadsheet);
        //保存的路径可自行设置
        //$file_name = '../'.$fileName . ".xlsx";
        //$writer->save($file_name);

        //第二种直接页面上显示下载
        header('Content-Encoding:UTF-8');
        header('Content-Transfer-Encoding:binary');

        $file_name = $fileName . ".xlsx";
        header('Content-Disposition:attachment;filename="'.$file_name.'"');

        if ($format == 'xlsx'){ header('Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); }
        if ($format == 'xls'){ header('Content-Type:application/vnd.ms-excel'); }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');//注意createWriter($spreadsheet, 'Xls') 第二个参数首字母必须大写

        $writer->save('php://output'); exit;
    }


}
