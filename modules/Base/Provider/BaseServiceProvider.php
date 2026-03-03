<?php

namespace Modules\Base\Provider;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

/**
 * notes: 模块依赖配置-基类
 * @author 陈鸿扬 | @date 2021/2/3 10:55
 */
class BaseServiceProvider extends ServiceProvider
{

    public function validatorBase(){

        //自定义验证规则 - test
        Validator::extend('test', function($attribute, $value, $parameters, $validator) {
            return  false; //返回对错
        });
        Validator::replacer('test', function($message, $attribute, $rule, $parameters) {
            return 'this is error validate msg'; //错误消息定制
        });

    }

}
