<?php
/**
 * notes: 逻辑类-基类
 * @author 陈鸿扬 | @date 2021/3/4 19:21
 */

namespace Modules\Base\Logic;

use Modules\Base\Error\BaseError;
use Modules\Base\Exception\Exception;

class BaseLogic
{
    //订单序列号
    public static function SerialNumber($prefix = null, $useId = null)
    {

        $dateTime = self::udate("YmdHis");
        $nicoTime = self::udate("u");
        $randA    = rand(0, 9999);
        $randB    = rand(0, 9999);

        $prefix = empty($prefix) ? 100 : $prefix;
        $useId  = empty($useId) ? $nicoTime : $useId;
        $useId  = substr($useId, -4);

        //3位数渠道号 + 日月时分秒 + 随机1位数A + 用户id后4位 + 随机1位数B
        $num = $prefix . $dateTime . $randA . $useId . $randB;
        //dd($num);//

        return $num;
    }

    //生成微秒
    public static function udate($strFormat = 'u', $uTimeStamp = null)
    {
        // If the time wasn't provided then fill it in
        if (is_null($uTimeStamp)) {
            $uTimeStamp = microtime(true);
        }
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

    //json 格式检查
    public static function jsonCheck($jsonStr)
    {
        $data = json_decode($jsonStr, true);
        if (empty($data)) {
            Exception::app(BaseError::code("WRONG_JSON_FORMAT"), BaseError::msg("WRONG_JSON_FORMAT"), __METHOD__);
        }
        return $data;
    }

    //过滤url链接中的 根域名, 返回相对路径.
    public static function urlPathFilter($url, &$match = null)
    {
        $path = $url;
        preg_match("/^http.*(\.com|\.cn)(.*$)/", $url, $match);
        if (isset($match[2])) {
            $path = $match[2];
        }
        return $path;
    }

    //字典 键值对 拆分成 [{ name:"a", value:"b" }]
    public static function mapKvSeparate($map,array $nameStrArr,$nameStr='name',$valueStr='value'){
        $newMap=[]; $keys = array_keys($nameStrArr);
        foreach ($map as $key=>$value){
            $searchIndex = array_search($key,$keys);
            if( $searchIndex !== false ){
                $keyStr = $nameStrArr["$key"];
                if( !empty($keyStr) ) { $key=$keyStr; }
                $currArr = ["$nameStr"=>$key,"$valueStr"=>$value];
                $newMap[$searchIndex]=$currArr;
            }
        }
        ksort($newMap);
        $newMap = array_values($newMap);
        return $newMap;
    }

    //字典 键值对 拆分成 [{ name:"a", value:"b" }]; 支持闭包再补充数据;
    public static function mapKvSepClosure($map,array $nameStrArr,\Closure $closure, $nameStr='name',$valueStr='value'){
        $newMap=[]; $keys = array_keys($nameStrArr);
        foreach ($map as $key=>$value){
            //寄存
            $currMapKey = $key; $currMapValue = $value;
            //搜索
            $searchIndex = array_search($key,$keys);
            if( $searchIndex !== false ){
                //获取自定义key名
                $keyStr = $nameStrArr["$key"];
                if( !empty($keyStr) ) { $key=$keyStr; }
                //拼接当前name/value结构
                $currArr = ["$nameStr"=>$key,"$valueStr"=>$value];
                //闭包处理
                $closure($currArr,$currMapKey,$currMapValue,$searchIndex);
                //收集结果
                $newMap[$searchIndex]=$currArr;
            }
        }
        ksort($newMap);
        $newMap = array_values($newMap);
        return $newMap;
    }

}