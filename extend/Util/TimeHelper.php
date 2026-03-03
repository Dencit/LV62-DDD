<?php
/**
 * notes: 日期时间-相关处理工具
 * @author 陈鸿扬 | @date 2021/3/16 11:01
 */

namespace Extend\Util;

class TimeHelper
{

    //生成微秒
    public static function udate($strFormat = 'u', $uTimeStamp = null)
    {
        // If the time wasn't provided then fill it in
        if (is_null($uTimeStamp)) { $uTimeStamp = microtime(true); }
        // Round the time down to the second
        $dtTimeStamp = floor($uTimeStamp);
        // Determine the millisecond value
        $intMilliseconds = round(($uTimeStamp - $dtTimeStamp) * 1000000);
        // Format the milliseconds as a 6 character string
        $strMilliseconds = str_pad($intMilliseconds, 6, '0', STR_PAD_LEFT);
        // Replace the milliseconds in the date format string
        // Then use the date function to process the rest of the string
        return date(preg_replace('`(?<!\\\\)u`', $strMilliseconds, $strFormat), $dtTimeStamp);
    }

    //生成订单号
    public static function SerialNumber($prefix=null,$useId=null){

        $dateTime = self::udate("YmdHis");
        $nicoTime = self::udate("u");
        $randA = rand(0, 9999);
        $randB = rand(0, 9999);

        $prefix = empty($prefix) ? 100 : $prefix;
        $useId = empty($useId) ? $nicoTime : $useId;
        $useId= substr($useId,-4);

        //3位数渠道号 + 日月时分秒 + 随机1位数A + 用户id后4位 + 随机1位数B
        $num= $prefix.$dateTime.$randA.$useId.$randB;
        //dd($num);//

        return $num;
    }


    /*
     * notes: 根据Y-m-d日期 生成统计区间
     * @author 陈鸿扬 | @date 2021/2/5 17:24
     */
    public function staticDateTime(&$startTime,&$endTime,&$currentDate=null,$date=null){
        if(!empty($date)){ //有date值 计算当天到23:59:59 的数据
            $currentDate = date('Y-m-d H:i:s',strtotime( $date.'' ));
            $startTime = date('Y-m-d H:i:s',strtotime( $date.'' ));
            $endTime = date('Y-m-d H:i:s',strtotime( $date.' +23 hour +59 minute +59 sec' ));
        }else{ //无date值 计算今天到23:59 的数据
            $currentDate = date('Y-m-d H:i:s',strtotime( date('Y-m-d').'' ));
            $startTime = date('Y-m-d H:i:s',strtotime( date('Y-m-d').'' ));
            $endTime = date('Y-m-d H:i:s',strtotime( date('Y-m-d').' +23 hour +59 minute +59 sec' ));
        }
        //var_dump($currentDate); var_dump($startTime); dd($endTime);//
    }


}