<?php

namespace Extend\AliYunSdk;

use Modules\Base\Error\BaseError;
use Modules\Base\Exception\Exception;
use Sms\Request\V20170525\SendSmsRequest as SendSms;
use Sms\Request\V20170525\SendBatchSmsRequest as SendBatchSms;
use Sms\Request\V20170525\QuerySendDetailsRequest as QuerySendDetails;

class SdkSms
{

    protected $client;

    public function __construct()
    {
        if (empty($this->client)) {
            //引入阿里云核心sdk
            include_once(base_path('extend/AliYunSdk/core/Config.php'));
            $accessKey    = config("conf.aliyun.access_key_id");
            $accessSecret = config('conf.aliyun.access_key_secret');
            $ipRegionInfo = ['region_id' => 'cn-shanghai', 'endpoint' => 'dysmsapi.aliyuncs.com'];

            $iClientProfile = \DefaultProfile::getProfile($ipRegionInfo['region_id'], $accessKey, $accessSecret);
            // 增加服务结点
            \DefaultProfile::addEndpoint($ipRegionInfo['region_id'], $ipRegionInfo['region_id'], "Dysmsapi", $ipRegionInfo['endpoint']);
            $this->client = new \DefaultAcsClient($iClientProfile);

        }
    }

    /**
     * Class SmsDemo
     * @param $mobile
     * @param $tplArr
     * @param $template
     * @return bool|mixed|\SimpleXMLElement
     */
    public function sendSms($mobile, $tplArr, $template)
    {
        $request = new SendSms();
        //可选-启用https协议
        $request->setProtocol("https");
        // 必填，设置短信接收号码
        $request->setPhoneNumbers($mobile);
        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName(config('conf.sms.sign_name'));
        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode($template);
        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam(json_encode($tplArr, JSON_UNESCAPED_UNICODE)); // 短信模板中字段的值
        // 可选，设置流水号
        $request->setOutId(time());
        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        //$request->setSmsUpExtendCode("1234567");
        $response = $this->client->getAcsResponse($request);
        //dd($response->Code);

        switch ($response->Code) {
            default :
                return false;
                break;
            case "OK":
                return true;
                break;
            case "isv.BUSINESS_LIMIT_CONTROL":
                Exception::app(BaseError::code("BUSINESS_LIMIT_CONTROL"), BaseError::msg("BUSINESS_LIMIT_CONTROL"), __METHOD__);
                break;
        }

    }

    /**
     * 批量发送短信
     * @return stdClass
     */
    public static function sendBatchSms()
    {
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendBatchSms();
        //可选-启用https协议
        //$request->setProtocol("https");
        // 必填:待发送手机号。支持JSON格式的批量调用，批量上限为100个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
        $request->setPhoneNumberJson(json_encode(array(
            "1500000000",
            "1500000001",
        ), JSON_UNESCAPED_UNICODE));
        // 必填:短信签名-支持不同的号码发送不同的短信签名
        $request->setSignNameJson(json_encode(array(
            "云通信",
            "云通信"
        ), JSON_UNESCAPED_UNICODE));
        // 必填:短信模板-可在短信控制台中找到
        $request->setTemplateCode("SMS_1000000");
        // 必填:模板中的变量替换JSON串,如模板内容为"亲爱的${name},您的验证码为${code}"时,此处的值为
        // 友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
        $request->setTemplateParamJson(json_encode(array(
            array(
                "name" => "Tom",
                "code" => "123",
            ),
            array(
                "name" => "Jack",
                "code" => "456",
            ),
        ), JSON_UNESCAPED_UNICODE));
        // 可选-上行短信扩展码(扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段)
        // $request->setSmsUpExtendCodeJson("[\"90997\",\"90998\"]");
        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);
        return $acsResponse;
    }

    /**
     * 短信发送记录查询
     * @return stdClass
     */
    public static function querySendDetails()
    {
        // 初始化QuerySendDetailsRequest实例用于设置短信查询的参数
        $request = new QuerySendDetails();
        //可选-启用https协议
        //$request->setProtocol("https");
        // 必填，短信接收号码
        $request->setPhoneNumber("12345678901");
        // 必填，短信发送日期，格式Ymd，支持近30天记录查询
        $request->setSendDate("20170718");
        // 必填，分页大小
        $request->setPageSize(10);
        // 必填，当前页码
        $request->setCurrentPage(1);
        // 选填，短信发送流水号
        $request->setBizId("yourBizId");
        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);

        return $acsResponse;
    }

}