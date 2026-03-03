<?php
namespace Modules\User\Errors;

use Modules\Base\Error\BaseErr;

/**
 * notes: 数据单元错误码
 * Class UserError
 * @package Modules\User\Errors
 */
class UserRootError extends BaseErr{

    protected static $data = [
        "ID_NOT_FOUND"         => ['code' => 201000, 'msg' => '用户ID 不存在'],
        "ID_NOT_UNIQUE"        => ['code' => 201001, 'msg' => '用户ID 已存在'],
        "BATCH_IDS_NOT_FOUND"  => ['code' => 201002, 'msg' => '批量数据中 有ID不存在'],
        "BATCH_IDS_NOT_UNIQUE" => ['code' => 201003, 'msg' => '批量数据中 有ID已存在'],
        "MOBILE_NOT_FOUND"     => ['code' => 201004, 'msg' => '手机号 不存在'],
        "MOBILE_NOT_UNIQUE"    => ['code' => 201005, 'msg' => '手机号 已存在'],
        "PASSWORD_WRONG"      => ['code' => 201006, 'msg' => '密码错误'],
    ];

    static function code($type){
       return self::$data[$type]['code'];
    }

    static function msg($type){
        return self::$data[$type]['msg'];
    }

}

