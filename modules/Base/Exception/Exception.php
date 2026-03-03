<?php
namespace Modules\Base\Exception;

use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * notes: 请求异常-基类
 * @author 陈鸿扬 | @date 2021/2/3 10:55
 */
class Exception extends HttpException
{

    public static function http($error_code,$msg,$method=__METHOD__){
        $method = str_replace("\\", "/",$method);
        $errMsg = PHP_EOL
            .'{'
                .'"http_exception":'
                    .'{'
                    .'"code":"'.$error_code.'",'
                    .'"msg":"'.$msg.'",'
                    .'"method":"'.$method.'"'
                    .'}'
            .'}';

        Log::channel("http_exception")->notice($errMsg);
        throw new HttpException( 400, $msg , null, [] , $error_code );
    }

    public static function app($error_code,$msg='',$method=__METHOD__){
        $method = str_replace("\\", "/",$method);
        $errMsg = PHP_EOL
            .'{'
                .'"app_exception":'
                    .'{'
                    .'"code":"'.$error_code.'",'
                    .'"msg":"'.$msg.'",'
                    .'"method":"'.$method.'"'
                    .'}'
            .'}';

        Log::channel("app_exception")->notice($errMsg);
        throw new AppException( 400, $msg , null, [] , $error_code );
    }


}