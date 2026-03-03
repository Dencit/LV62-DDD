<?php

namespace Modules\Base\Exception;

/**
 * notes: 应用错误-基类
 * @author 陈鸿扬 | @date 2021/2/3 10:55
 */
class BaseErr
{

    protected static $data = [
    ];

    static function code($type)
    {
        return self::$data[$type]['code'];
    }

    static function msg($type)
    {
        return self::$data[$type]['msg'];
    }

}