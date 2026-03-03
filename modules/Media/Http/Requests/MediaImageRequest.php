<?php

namespace Modules\Media\Http\Requests;

use Modules\Base\Request\BaseRequest;

/**
 * notes: 应用层-输入验证类
 * desc: 只在此类 统一校验输入数据.
 * 内置规则: https://laravelacademy.org/post/9547
 */
class MediaImageRequest extends BaseRequest
{
    //验证规则
    protected $rules = [
        //@rules
        "id"         => "integer|gt:0|between:0,20",
        "host"       => "string|between:0,255",
        "uri"        => "string|between:0,255",
        "type"       => "integer|in:1,2",
        "status"     => "integer|in:1,2",
        "created_at" => "date",
        "updated_at" => "date",
        "deleted_at" => "date",
        //@rules
        "suffix"     => "string",
    ];

    //
    protected $messages = [
        //@messages
        'id.integer' => '图片媒介表id 必须是整数',
        'id.gt'      => '图片媒介表id 必须大于0',
        'id.gte'     => '图片媒介表id 必须大于等于0',
        'id.max'     => '图片媒介表id 超出最大值',
        'id.min'     => '图片媒介表id 超出最小值',
        'id.in'      => '图片媒介表id 数值超出许可范围',
        'id.between' => '图片媒介表id 最大长度是 20',

        'host.string'     => '根域名 包含非法字符-只能是字符串',
        'host.alpha'      => '根域名 包含非法字符-只能是/字母',
        'host.alpha_num'  => '根域名 包含非法字符-只能是/字母/数字',
        'host.alpha_dash' => '根域名 包含非法字符',
        'host.between'    => '根域名 最大长度是 255',

        'uri.string'     => '相对路径 包含非法字符-只能是字符串',
        'uri.alpha'      => '相对路径 包含非法字符-只能是/字母',
        'uri.alpha_num'  => '相对路径 包含非法字符-只能是/字母/数字',
        'uri.alpha_dash' => '相对路径 包含非法字符',
        'uri.between'    => '相对路径 最大长度是 255',

        'type.integer' => '图片用途：1头像, 2封面, 必须是整数',
        'type.gt'      => '图片用途：1头像, 2封面, 必须大于0',
        'type.gte'     => '图片用途：1头像, 2封面, 必须大于等于0',
        'type.max'     => '图片用途：1头像, 2封面, 超出最大值',
        'type.min'     => '图片用途：1头像, 2封面, 超出最小值',
        'type.in'      => '图片用途：1头像, 2封面, 数值超出许可范围',
        'type.between' => '图片用途：1头像, 2封面, 最大长度是 3',

        'status.integer' => '启用状态：1未启用，2启用中，3禁用 必须是整数',
        'status.gt'      => '启用状态：1未启用，2启用中，3禁用 必须大于0',
        'status.gte'     => '启用状态：1未启用，2启用中，3禁用 必须大于等于0',
        'status.max'     => '启用状态：1未启用，2启用中，3禁用 超出最大值',
        'status.min'     => '启用状态：1未启用，2启用中，3禁用 超出最小值',
        'status.in'      => '启用状态：1未启用，2启用中，3禁用 数值超出许可范围',
        'status.between' => '启用状态：1未启用，2启用中，3禁用 最大长度是 3',

        'created_at.date'        => '创建时间|注册时间 日期时间格式有误',
        'created_at.date_format' => '创建时间|注册时间 自定义日期格式有误',

        'updated_at.date'        => '更新时间 日期时间格式有误',
        'updated_at.date_format' => '更新时间 自定义日期格式有误',
        'updated_at.required'    => '更新时间 不能为空',

        'deleted_at.date'        => '删除时间 日期时间格式有误',
        'deleted_at.date_format' => '删除时间 自定义日期格式有误',
        'deleted_at.required'    => '删除时间 不能为空',

        //@messages
    ];

    //edit 验证场景 定义方法
    //例子: $this->only(['name','age']) ->append('name', 'min:5') ->remove('age', 'between') ->append('age', 'require|max:100');
    public function sceneSaveOssAuth()
    {
        return $this
            ->append('type', 'required')
            ->append('suffix', 'required')
            ;
    }

    public function sceneSaveOssUri()
    {
        return $this
            ->append('type', 'required')
            ->append('uri', 'required')
            ;
    }

    public function sceneSave()
    {
        //return $this->append('id', 'required');
    }

    public function sceneUpdate()
    {
        //return $this->append('id', 'required');
    }

    public function scenePatch()
    {
        //return $this->append('id', 'required');
    }

    public function sceneRead()
    {
        //return $this->append('id', 'required');
    }

    public function sceneDelete()
    {
        //return $this->append('id', 'required');
    }

}