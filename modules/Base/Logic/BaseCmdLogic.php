<?php
/**
 * notes: 命令行逻辑类-基类
 * @author 陈鸿扬 | @date 2022/6/17 22:13
 */

namespace Modules\Base\Logic;

use Illuminate\Support\Facades\Redis;

class BaseCmdLogic
{
    //索引表版本号
    protected static $version = 0;


    //#公共区域 start

    //截取host文本 - 不带"http://"
    protected function getHostStr( string $url)
    {
        preg_match("/^.*:\/\/(.*)([\/]{1}.*$)/", $url, $m);
        $m = explode('/',$m[1] ?? '');
        return $m[0] ?? '';
    }

    //截取host参数文本 - 从"?"开始 截取连接参数
    protected function getHostParamStr(string $fullUrl, &$newUrl = null, &$param = null)
    {
        $matchArr = explode('?', $fullUrl ?? '');
        $newUrl = $matchArr[0] ?? '';
        $param = $matchArr[1] ?? '';
        if (!empty($param)) {
            $newParamArr = [];
            $paramArr = explode('&', $param);
            array_walk($paramArr, function (&$equalStr, $index) use (&$newParamArr) {
                $equalArr = explode('=', $equalStr);
                $newParamArr[$equalArr[0]] = $equalArr[1]??'';
            });
            unset($newParamArr['token']);
            ksort($newParamArr);
            $param = http_build_query($newParamArr);
        }
        return $param;
    }

    //获取最后处理记录ID
    public function getLastDoneId($tableName, $firstId = 1)
    {
        $db = config("database.redis.default.select");
        Redis::select($db);

        //缓存名 + 版本号
        $taskName = 'move_data';
        if (!empty(static::$version)) {
            $taskName .= '-' . static::$version;
        }

        $id = $firstId;
        $key = config("database.redis.default.prefix") . $taskName . ":" . $tableName . ".LastId";

        $res = Redis::get($key);
        if (!empty($res)) {
            $id = $res;
        }
        return $id;
    }

    //设置最后处理记录ID
    public function setLastDoneId($tableName, $lastDoneId)
    {
        $db = config("database.redis.default.select");
        Redis::select($db);

        //缓存名 + 版本号
        $taskName = 'move_data';
        if (!empty(static::$version)) {
            $taskName .= '-' . static::$version;
        }

        $key = config("database.redis.default.prefix") . $taskName . ":" . $tableName . ".LastId";

        $res = Redis::set($key, $lastDoneId);
        //Redis::expire($key,$expire);

        return $res;
    }

    //清除最后处理记录ID
    public function dropLastDoneId($tableName, $firstId = 1)
    {
        $db = config("database.redis.default.select");
        Redis::select($db);

        //缓存名 + 版本号
        $taskName = 'move_data';
        if (!empty(static::$version)) {
            $taskName .= '-' . static::$version;
        }

        $id = $firstId;
        $key = config("database.redis.default.prefix") . $taskName . ":" . $tableName . ".LastId";

        $res = Redis::del($key);
        if (!empty($res)) {
            $id = $res;
        }
        return $id;
    }


    //#公共区域 end

}