<?php

namespace Modules\Admin\Errors;

use Modules\Base\Exception\BaseErr;

/**
 * notes: 根模块-总错误码
 * desc: 错误码区间,根据模块下的 doc.md 定义来设置. 注意 按数据单元做好注释, 每个单元错误码预留20位数间隔.
 */
class AdminRootError extends BaseErr
{
    protected static $data = [
        "ID_NOT_FOUND"        => ['code' => 202000, 'msg' => 'ID 不存在'],
        "ID_NOT_UNIQUE"       => ['code' => 202001, 'msg' => 'ID 已存在'],
        "MOBILE_NOT_FOUND"    => ['code' => 202003, 'msg' => '手机号 不存在'],
        "MOBILE_NOT_UNIQUE"   => ['code' => 202004, 'msg' => '手机号 已存在'],
        "PASSWORD_WRONG"     => ['code' => 202005, 'msg' => '密码错误'],
        "DON_DELETE_YOU_SELF" => ['code' => 202006, 'msg' => '不能自己删除自己']
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
