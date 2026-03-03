<?php

namespace Modules\Base\Console;

use Illuminate\Console\Command;

/**
 * notes: 数据单元指令 基类
 * @author 陈鸿扬 | @date 2022/5/25 12:47
 */
class BaseCmd extends Command
{

#v常用函数区域

    //日区间转换
    protected function dayDate(&$beginDate, &$endDate)
    {
        if (empty($beginDate)) {
            $now = 'now';
            //$now = '2021-12-03 00:03:00';//
            $beginDate = $endDate = date("Y-m-d", strtotime($now . " -1 day"));
        }
        $endDate = $beginDate;
        $beginDate = $beginDate . ' 00:00:00';
        $endDate = $endDate . ' 23:59:59';
        //dd($beginDate,$endDate);//
    }

    //周区间转换
    protected function weekDate(&$beginDate, &$endDate)
    {
        if (empty($beginDate) || empty($endDate)) {
            $now = 'now';
            //$now = '2021-12-03 00:03:00';//
            $beginDate = date("Y-m-d", strtotime($now . " -1 week"));
            $endDate = date("Y-m-d", strtotime($now . " -1 day"));
        }

        $beginDate = $beginDate . ' 00:00:00';
        $endDate = $endDate . ' 23:59:59';
        //dd($beginDate,$endDate);//
    }

    //月区间转换
    protected function monthDate(&$beginDate, &$endDate)
    {
        if (empty($beginDate) || empty($endDate)) {
            $now = 'now';
            //$now = '2021-03-01 00:03:00';//
            $beginDate = date("Y-m", strtotime($now . " -1 month")) . "-01";
            $endDate = date("Y-m-d", strtotime($now . " -1 day"));
        }

        $beginDate = $beginDate . ' 00:00:00';
        $endDate = $endDate . ' 23:59:59';
        //dd($beginDate,$endDate);//
    }

    //月区间转换 - 月区间平移
    protected function monthBeforeDate(&$beginDate, &$endDate)
    {
        if (empty($beginDate) || empty($endDate)) {
            $now = 'now';
            //$now = '2022-03-24 00:03:00';//
            $beginDate = date("Y-m-d", strtotime($now . " -1 month"));
            $endDate = date("Y-m-d", strtotime($now . " -1 day"));
        }

        $beginDate = $beginDate . ' 00:00:00';
        $endDate = $endDate . ' 23:59:59';
        //dd($beginDate,$endDate);//
    }

    //年区间转换
    protected function yearDate(&$beginDate, &$endDate)
    {
        if (empty($beginDate) || empty($endDate)) {
            $now = 'now';
            //$now = '2022-01-01 00:03:00';//
            $beginDate = date("Y-m", strtotime($now . " -1 year")) . "-01";
            $endDate = date("Y-m-d", strtotime($now . " -1 day"));
        }
        $beginDate = $beginDate . ' 00:00:00';
        $endDate = $endDate . ' 23:59:59';
        //dd($beginDate,$endDate);//
    }

    //年区间转换 - 年区间平移
    protected function yearBeforeDate(&$beginDate, &$endDate)
    {
        if (empty($beginDate) || empty($endDate)) {
            $now = 'now';
            //$now = '2023-01-01 00:03:00';//
            $beginDate = date("Y-m-d", strtotime($now . " -1 year"));
            $endDate = date("Y-m-d", strtotime($now . " -1 day"));
        }
        $beginDate = $beginDate . ' 00:00:00';
        $endDate = $endDate . ' 23:59:59';
        //dd($beginDate,$endDate);//
    }


#^常用函数区域

}