<?php

namespace app\Http\Middleware;

use Modules\Base\Error\BaseError;
use Modules\Base\Exception\Exception;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Modules\Base\Middleware\JsonWebToken;

class BaseAuth extends Middleware
{

    //检查授权
    public function apiAuth($request){
        $user = null;

        $token =  $request->header('token');
        if( empty($token) ){
            Exception::app(BaseError::code('TOKEN_MUST'),BaseError::msg('TOKEN_MUST'),__METHOD__);
        }

        $user = JsonWebToken::checkToken($token);

        return $user;
    }
    
    //检查角色
    public function userRole($role){

        switch ($role){
            default : Exception::app(BaseError::code('USER_ROLE_FAIL'),BaseError::msg('USER_ROLE_FAIL'),__METHOD__);
                break;
            case 'user':

                break;
            case 'adm':

                break;
            case 'sys':

                break;
        }

    }

}