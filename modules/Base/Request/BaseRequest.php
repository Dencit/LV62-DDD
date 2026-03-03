<?php

namespace Modules\Base\Request;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Base\Error\BaseError;
use Modules\Base\Exception\Exception;

/**
 * notes: 输入验证器-基类
 * @author 陈鸿扬 | @date 2021/2/3 10:55
 */
class BaseRequest
{
    use ValidatesRequests;

    protected $rules = [];

    protected $sceneRules = [];

    protected $messages = [];

    //验证默认
    public function checkValidate($requestInput)
    {
        $validator = Validator::make($requestInput, $this->rules, $this->messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $validator->validate();
    }

    //验证场景
    public function checkSceneValidate($sceneStr, $requestInput)
    {
        $newRules  = $this->sceneRules($sceneStr); //生成新rules
        $validator = Validator::make($requestInput, $newRules, $this->messages);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $validator->validate();
    }

    //获取场景验证rules
    protected function sceneRules($sceneStr)
    {
        $sceneMethodName = 'scene' . ucwords($this->toHumpName($sceneStr));
        $methodExits     = method_exists($this, $sceneMethodName);
        if (!$methodExits) {
            Exception::app(BaseError::code('SCENE_VALIDATE_PARAM_FAIL'), BaseError::msg('SCENE_VALIDATE_PARAM_FAIL'));
        }
        $this->{$sceneMethodName}(); //触发相应场景函数 补充验证条件
        $newRules = $this->sceneRules;
        return $newRules;
    }

    //添加验证条件 - 链式
    protected function append($key, $paramStr)
    {
        if (empty($this->sceneRules)) {
            $this->sceneRules = $this->rules;
        }
        if (!empty($this->sceneRules[$key])) {
            //存在过滤设置,则补充
            $this->sceneRules[$key] .= '|' . $paramStr;
        } else {
            //不存在过滤设置,则新增
            $this->sceneRules[$key] = $paramStr;
        }
        return $this;
    }

    //小写名称转驼峰 - 如 user_name : userName
    public function toHumpName($name)
    {
        $nameArr = explode('_', $name);
        $newName = '';
        foreach ($nameArr as $ind => $str) {
            if ($ind == 0) {
                $newName .= strtolower($str);
            } else {
                $newName .= ucwords($str);
            }
        }
        return $newName;
    }

    //json 验证规则
    protected function jsonCheck($value, $rule = '', $data = '', $field = '')
    {
        $data = json_decode($value, true);
        if (empty($data)) {
            return $field . ' 必须是非空的Json格式';
        }
        return true;
    }

}