<?php

namespace Modules\Media\Errors;

use Modules\Base\Exception\BaseErr;

/**
 * notes: 根模块-总错误码
 * desc: 错误码区间,根据模块下的 doc.md 定义来设置. 注意 按数据单元做好注释, 每个单元错误码预留20位数间隔.
 */
class MediaRootError extends BaseErr
{
    protected static $data = [
        "ID_NOT_FOUND"         => ['code' => 203000, 'msg' => 'ID 不存在'],
        "ID_NOT_UNIQUE"        => ['code' => 203001, 'msg' => 'ID 已存在'],
        "BATCH_IDS_NOT_FOUND"  => ['code' => 203002, 'msg' => '批量数据中 有ID不存在'],
        "BATCH_IDS_NOT_UNIQUE" => ['code' => 203003, 'msg' => '批量数据中 有ID已存在'],
        //media_image 表
        "FORMAT_WRONG"         => ['code' => 203000, 'msg' => '图片格式错误'],
        "SIZE_EXCESS"          => ['code' => 203001, 'msg' => '图片超出大小范围'],
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
