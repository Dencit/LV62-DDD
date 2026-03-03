<?php
/**
 * notes: seeder 数据填充 基类
 * @author 陈鸿扬 | @date 2021/3/16 17:23
 */

namespace Modules\Base\Database;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaseSeeder extends Seeder
{

    function createSecret($length = 8){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }


    //获取旧数据 - 去重
    protected function oldDataExit($name,$id,$values){
        $valueStr=''; foreach ($values as $k=>$v){ $valueStr.='\''.$v.'\','; }
        $valueStr = trim($valueStr,',');
        //
        $tableName= config('database.connections.mysql.prefix').$name;
        $query =
            'SELECT '.$id.' FROM '.$tableName.
            ' where `'.$id.'` In ('.$valueStr.')'
        ;
        //
        $rows = DB::select($query);
        if($rows){ $rows = array_column($rows,$id); return $rows; }
        return [];
    }

}