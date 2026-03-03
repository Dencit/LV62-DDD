<?php

namespace Modules\Base\Middleware;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Modules\Base\Error\BaseError;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Modules\Base\Exception\Exception;


class  JsonWebToken
{

    static function signToken(&$data)
    {
        $userId       = $data['user_id'];
        $scopeId      = $data['scope_id']; //unset($data['client_id']);
        $clientId     = $data['client_id']; //unset($data['client_id']);
        $clientSecret = $data['client_secret'];
        unset($data['client_secret']); //加密中常用的 盐salt
        $exp_time = $data['exp_time'];

        $signer  = new Sha256(); //签名对象
        $builder = new Builder();
        $builder
            ->setAudience('L57')//签发人
            ->setIssuedAt(time())//签发时间
            ->setExpiration($exp_time)//过期时间
            ->setId($userId, true)->set('scope_id', $scopeId)->set('client_id', $clientId)
            ->set('data', $data)
            ->sign($signer, $clientSecret);

        $token = (string)$builder->getToken();  //根据参数生成了 token

        return $token;
    }

    //验证token
    static function checkToken($token)
    {

        $redis = Redis::connection();
        $redis->select(0);
        //根据 TOKEN 获取 SECRET
        $clientSecret = $redis->get($token);
        if (!$clientSecret) {
            Exception::http(BaseError::code('TOKEN_FAIL'), BaseError::msg('TOKEN_FAIL'), __METHOD__);
        }

        $tokenParse = (new Parser())->parse((string)$token);
        //TOKEN 校验
        $verify = $tokenParse->verify(new Sha256(), (string)$clientSecret);
        if (!$verify) {
            Exception::http(BaseError::code('TOKEN_VERIFY_FAIL'), BaseError::msg('TOKEN_VERIFY_FAIL'), __METHOD__);
        }

        $status = ["code" => 2];
        try {
            //TOKEN 解密
            $decoded     = json_decode(json_encode($tokenParse->getClaims()));
            $res['code'] = 1;
            $res['data'] = $decoded->data;
            return $res['data'];
        } catch (Exception $e) { //其他错误
            $status['msg'] = "TOKEN 解密异常";
            return $status;
        }
    }

}