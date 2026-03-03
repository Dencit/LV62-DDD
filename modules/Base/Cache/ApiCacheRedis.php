<?php

namespace Modules\Base\Cache;

use Illuminate\Support\Facades\Redis;

/**
 * notes: API缓存-基类
 * @author 陈鸿扬 | @date 2021/2/3 10:55
 */
class ApiCacheRedis
{
    protected static $Redis;
    protected $sign;

    public function __construct($table_num=0)
    {
        $this->sign = $this->hashSign(0);
        self::$Redis = Redis::class;

        if( $table_num==0 ){
            Redis::select(0);
        }else{
            //控制器数据缓存 统一放 第2个库
            Redis::select($table_num);
        }

        return self::$Redis;
    }


    /*
     * 缓存闭包处理
     */
    public function Cache($class,$function,$mineKey='',\Closure $closure,$expire=300){
        //缓存
        if($expire>0){
            //api查询缓存
            $keyName = implode("_", (explode('\\',$class)) ).'_'.$function;
            $data = $this->getDataByMineKey($keyName,$mineKey);
            //var_dump($data);
            if (!$data) {
                $result = $closure();
                //api设置缓存
                $this->setDataByMineKey($keyName, serialize($result), $mineKey, $expire);
            }else{
                $result = unserialize($data);
            }
        }
        //不缓存
        else{
            $result = $closure();
        }

        return $result;
    }


    public function getData($key){
        if(request()->query('_time')==1){
            return false;
        }else{
            $key=config("database.redis.default.prefix").$key.$this->sign;

            return self::$Redis::get($key);
        }
    }
    public function getDataByMineKey($key,$mine=null){
        if(request()->query('_time')==1){
            return false;
        }else{
            $key=config("database.redis.default.prefix").$key.$this->mineKey($mine).$this->sign;

            return self::$Redis::get($key);
        }
    }


    public function setData($key,$value,$mine=null,$expire=300){
        $key=config("database.redis.default.prefix").$key.$this->sign;

        self::$Redis::set($key,$value);
        $result = self::$Redis::expire($key,$expire);

        return $result;
    }
    public function setDataByMineKey($key,$value,$mine=null,$expire=300){
        $key=config("database.redis.default.prefix").$key.$this->mineKey($mine).$this->sign;

        self::$Redis::set($key,$value);
        $result = self::$Redis::expire($key,$expire);

        return $result;
    }


    public function mineKey($key=null){
        if( $key!=null ){
            return ':mine_key_'.$key;
        }else{
            return '';
        }
    }
    public function hashSign($type=null){
        $normalData =  request()->query();
        unset($normalData['_time']); //排除实时字段
        $queryStr=""; //待拼接字符串
        ksort($normalData);
        foreach ($normalData as $k=>$v){
            if( !empty($v) ){ $queryStr.= $k."=".$v."&";}
        }
        $queryStr = trim($queryStr,"&");
        if($type){
            $newShaSign = hash("sha256", $queryStr);
        }else{
            $newShaSign = $queryStr;
        }
        return $newShaSign;
    }

}