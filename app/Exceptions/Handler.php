<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Modules\Base\Exception\AppException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // 添加自定义异常处理机制
        $msg=[
            "error"=>$exception->getMessage(),
            "message"=>$exception->getMessage(),
            "code"=>$exception->getCode()
        ];
        $msg=$this->isDebugMsg($exception,$msg);

        // 参数验证错误
        if ($exception instanceof ValidationException) {
            $msg['error'] = $this->validateErrorString($exception);
            $msg['message'] =  $this->validateMsgString($exception);
            $msg['code']=422;
            return response($msg,422);
        }

        // http 请求异常
        if ($exception instanceof HttpException ) {
            $msg['code']=$exception->getStatusCode();
            return response($msg,400);
        }

        // app 请求异常
        if ($exception instanceof AppException ) {
            $msg['code']=$exception->getCode();
            return response($msg,400);
        }

        //详细的异常日志
        $this->setErrorLog($exception);

        //其他错误 默认数据结构
        $msg=$this->isDebugMsg($exception,$msg);
        return response($msg,400);

        // 其他错误交给系统处理
        //return parent::render($request, $exception);
    }


    /*
     * notes: 异常信息过滤 - 对生产环境隐藏敏感信息
     * @author 陈鸿扬 | @date 2021/1/26 15:28
     */
    public function isDebugMsg($exception,$msg){
        $debug = Config('app.debug');
        if($debug){
            $msg['file']=$exception->getFile();
            $msg['line']=$exception->getLine();
            $msg['trace']=$exception->getTrace();
        }
        return $msg;
    }

    /*
     * notes: 详细的异常日志
     * @author 陈鸿扬 | @date 2021/1/31 3:10
     */
    public function setErrorLog($exception){

        $error = [
            'request'=>[
                'method' =>  \request()->method(),
                'header' => \request()->header(),
                'url' => \request()->url(),
                'query' => \request()->query(),
                'param' => \request()->input()
            ],
            'error'=>[
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]
        ];
        Log::error('ERROR_LOG',$error);
    }

    /*
     * notes: 验证器异常-描述转换
     * @author 陈鸿扬 | @date 2021/1/26 15:28
     */
    public function validateErrorString($exception){
        $error = '参数错误';
        //获取第一个报错字段的描述
        $errArr = $exception->errors();
        if(count($errArr)>0) {
            foreach ($errArr as $key => $val) {
                $error = implode(' & ', $val);
                break;
            }
        }
        return $error;
    }

    /*
     * notes:验证器异常-描述转换
     * @author 陈鸿扬 | @date 2021/1/26 15:29
     */
    public function validateMsgString($exception){
        $msg = $exception->getMessage();
        if($msg =='The given data was invalid.'){
            $msg='输入参数错误';
        }
        return $msg;
    }

}
