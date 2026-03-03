<?php

namespace Modules\Demo\Http\Requests;

use Modules\Base\Request\BaseRequest;

/**
 * notes: 数据单元 输入验证器
 * doc: https://laravelacademy.org/post/9547
 * @author 陈鸿扬 | @date 2021/2/3 10:01
 */
class EsSampleRequest extends BaseRequest
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
    public function sceneTableSave()
    {
        //return $this->append('name', 'require');
    }

    public function sceneSave()
    {
        //return $this->append('id', 'require')->append('name', 'require');
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