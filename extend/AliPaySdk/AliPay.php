<?php

namespace Extend\AliPaySdk;

use App\Libs\AopClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as FcRequest;
use Modules\Base\Error\BaseError;
use Modules\Base\Exception\Exception;

class AliPay
{

    protected static $Aop;

    protected function getPemString($path)
    {
        $pemFile = file_get_contents($path);
        $pemFile = preg_replace('/\\n/', '', $pemFile);
        preg_match("/KEY-----(.*)-----END/", $pemFile, $m);
        if (isset($m[1])) {
            return $m[1];
        }
        return null;
    }

    public function __construct()
    {
        $ali_pay = config("conf.ali_pay");

        $appId                 = $ali_pay["app_id"];
        $alipayRsaPublicKeyPem = $ali_pay["alipay_rsa_public_key"];
        $alipay_rsa_public_key = file_get_contents($alipayRsaPublicKeyPem);
        //dd($alipay_rsa_public_key);//
        $rsaPublicKeyPem = $ali_pay["rsa_public_key_pem"];
        $rsa_public_key  = file_get_contents($rsaPublicKeyPem);
        //dd($rsa_public_key);//
        $rsaPrivateKeyPem = $ali_pay["rsa_private_key_pem"];
        $rsa_private_key  = file_get_contents($rsaPrivateKeyPem);
        //dd($rsa_private_key);//

        $gatewayUrl = $ali_pay["gateway_url"];

        if (empty($aop)) {

            $aop                     = new AopClient;
            $aop->gatewayUrl         = $gatewayUrl;
            $aop->appId              = $appId;
            $aop->alipayrsaPublicKey = $alipay_rsa_public_key;
            $aop->rsaPrivateKey      = $rsa_private_key;
            $aop->signType           = "RSA2";
            $aop->format             = "json";
            $aop->charset            = "UTF-8";

            self::$Aop = $aop;
        }

    }

    public function appPay(Request $request, $No)
    {
        $ali_pay = config("conf.ali_pay");

        $notify_url      = $ali_pay["notify_url"];
        $timeout_express = $ali_pay["timeout_express"];
        //dd($notify_url);//

        $AppPayRequest = new \AlipayTradeAppPayRequest();
        $yuan_money    = floatval($request->input("cash")); //元

        $content    = [
            "body"            => "金币充值",
            "subject"         => "个人充值",
            "out_trade_no"    => $No,
            "timeout_express" => $timeout_express,
            "total_amount"    => $yuan_money,
            "product_code"    => "QUICK_MSECURITY_PAY",
        ];
        $bizcontent = json_encode($content);
        $AppPayRequest->setNotifyUrl($notify_url);
        $AppPayRequest->setBizContent($bizcontent);

        $response = self::$Aop->sdkExecute($AppPayRequest);
        //dd($response);//

        if (!empty($response)) {
            return $response;
        }
        return false;
    }

    public function appPayBack(&$arr = [])
    {
        $arr  = $requestInput = FcRequest::input();
        $flag = self::$Aop->rsaCheckV1($requestInput, null, "RSA2");
        //dd($flag);//

        if (!$flag) {
            Log::info(__METHOD__, urldecode(file_get_contents("php://input")));
            Log::info(__METHOD__, $arr);
            Log::debug(__METHOD__, ["error" => "notify_check_fail!"]);

            Exception::app(BaseError::code("REQUEST_FAIL"), BaseError::msg("REQUEST_FAIL"), __METHOD__);
        } else {
            Log::debug(__METHOD__, ["msg" => "notify_check_success!"]);
        }

        return $flag;
    }

}