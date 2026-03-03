<?php
/**
 * notes: http请求-相关参数工具
 * @author 陈鸿扬 | @date 2021/3/16 10:59
 */

namespace Extend\Util;

use Modules\Base\Error\BaseError;
use Modules\Base\Exception\Exception;

class RequestHelper
{

    //获取 header VERSION - ABC_1.0.0_ID_13
    public static function getVersion($header){
        $verData = [
            'body'=>'NONE_0.0.0',
            'client'=>'none','version'=>'0.0.0',
            'id'=>0
        ];
        if( isset($header['version']) ){
            $version = explode('_',$header['version']);
            if( isset($version[0])&&isset($version[1]) ){
                $verData['body']= ($version[0]).'_'.($version[1]);
            }
            if( isset($version[0]) ){ $verData['client'] = $version[0]; }
            if( isset($version[1]) ){ $verData['version'] = $version[1]; }
            if( isset($version[2])&&isset($version[3]) ){
                $key = strtolower( $version[2] );
                $verData[  $key ] = $version[3];
            }
        }
        return $verData;
    }

    //json 格式检查
    public static function jsonCheck($jsonStr){
        $data=json_decode($jsonStr,true);
        if( empty($data) ){
            Exception::http( BaseError::code("WRONG_JSON_FORMAT") , BaseError::msg("WRONG_JSON_FORMAT") ,__METHOD__);
        }
        return $data;
    }

}