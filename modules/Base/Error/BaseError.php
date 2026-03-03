<?php

namespace Modules\Base\Error;

/**
 * notes: 基本错误码
 * @author 陈鸿扬 | @date 2021/2/3 10:55
 */
class BaseError extends BaseErr
{

    protected static $data = [
        "REQUEST_SUCCESS"               => ['code' => 100000, 'msg' => '请求成功'],
        "REQUEST_FAIL"                  => ['code' => 100001, 'msg' => '请求失败'],
        "WRONG_PARAM"                   => ['code' => 100002, 'msg' => '参数错误'],

        //SDK错误码
        "BUSINESS_LIMIT_CONTROL"        => ['code' => 110001, 'msg' => '发送过于频繁 短信验证码对同一个手机号码发送短信验证码，支持1条/分钟，5条/小时 ，累计10条/天'],

        //全局错误码 限制在 150000-199999 之间, 重新分配code
        "WHERE_SEARCH_OPERATOR_FAIL"    => ['code' => 150000, 'msg' => '_WHERE 查询表达式错误. 格式: k1/v1,k2/v2 '],
        "WHERE_IN_SEARCH_OPERATOR_FAIL" => ['code' => 150001, 'msg' => '_WHERE_IN 查询表达式错误. 格式: k1/v1,v2|k2/v1,v2 '],
        "TOKEN_MUST"                    => ['code' => 150002, 'msg' => '需要 TOKEN 授权'],
        "TOKEN_FAIL"                    => ['code' => 150003, 'msg' => '授权 TOKEN 有误'],
        "TOKEN_VERIFY_FAIL"             => ['code' => 150004, 'msg' => '授权 TOKEN 校验失败'],
        "AUTH_SCOPE_FAIL"               => ['code' => 150005, 'msg' => '超出设定权限范围'],
        "WRONG_BATCH_DATA"              => ['code' => 150006, 'msg' => '批处理数据输入错误'],
        "SCENE_VALIDATE_PARAM_FAIL"     => ['code' => 150007, 'msg' => '场景验证参数错误']
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

