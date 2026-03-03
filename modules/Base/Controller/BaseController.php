<?php

namespace Modules\Base\Controller;

use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Modules\Base\Error\BaseError;
use Modules\Base\Exception\Exception;


/**
 * notes: 数据单元控制器-基类
 * @author 陈鸿扬 | @date 2021/2/3 10:55
 */
class BaseController extends Controller
{
    use ValidatesRequests;

    //Request实例
    protected $request;

    public function __construct(Request $request){
        $this->request = $request;
    }

    //获取中间件 解密token 得到的数据
    public function auth($key=null){
        if($key){ return $this->request->auth->{$key}; }
        return $this->request->auth;
    }

    //批处理闭包
    protected function batchDone($batchData,\Closure $closure){
        foreach ($batchData as $ind=>$item){
            if( is_string($item) ){ Exception::app(BaseError::code('WRONG_BATCH_DATA'),BaseError::msg('WRONG_BATCH_DATA')); }
            $closure($item);
        }
    }
    //数组排除输入字段
    protected function arrayExcept(&$array,$rules){
        if( is_array($rules) ){
            foreach ($rules as $ind=>$val){
                if(isset($array[$val])){ unset($array[$val]); }
            }
        }
    }
    //数组限制输入字段
    protected function arrayOnly(&$array,$rules){
        if( is_array($rules) ){ $temp=[];
            foreach ($rules as $ind=>$val){
                if(isset($array[$val])){ $temp[$val]=$array[$val]; }
                $array = $temp;
            }
        }
    }

}