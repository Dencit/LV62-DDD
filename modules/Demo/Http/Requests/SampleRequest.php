<?php

namespace Modules\Demo\Http\Requests;

use Modules\Base\Request\BaseRequest;

/**
 * notes: 应用层-输入验证类
 * desc: 只在此类 统一校验输入数据.
 * 内置规则: https://laravelacademy.org/post/9547
 */
class SampleRequest extends BaseRequest
{
    //验证规则
    protected $rules = [
        //@rules
        "id"     => "integer|gt:0",
        "name"   => "string|between:0,255",
        "type"   => "integer|in:1,2",
        "status" => "integer|in:1,2",
        //@rules
    ];

    //
    protected $messages = [
        //@messages
        "id.required" => "id 不能为空",
        "id.gt"       => "id 必须大于0",

        "name.required" => 'name 不能为空',
        "name.between"  => "name 字符长度在0-255之间",

        "type.required" => "type 不能为空",
        "type.in"       => "类型 在1-2之间",

        "status.required" => "status 不能为空",
        "status.in"       => "状态 在1-2之间",
        //@messages
    ];

    //edit 验证场景 定义方法
    //例子: $this->only(['name','age']) ->append('name', 'min:5') ->remove('age', 'between') ->append('age', 'require|max:100');
    public function sceneSave()
    {
        //return $this->append('id','required');
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