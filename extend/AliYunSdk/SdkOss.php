<?php

namespace Extend\AliYunSdk;

use DateTime;
use Illuminate\Support\Facades\Log;
use JohnLui\AliyunOSS;
use Modules\Base\Exception\Exception;

class SdkOss
{
    /* 城市名称：
     *  经典网络下可选：杭州、上海、青岛、北京、张家口、深圳、香港、硅谷、弗吉尼亚、新加坡、悉尼、日本、法兰克福、迪拜
     *  VPC 网络下可选：杭州、上海、青岛、北京、张家口、深圳、硅谷、弗吉尼亚、新加坡、悉尼、日本、法兰克福、迪拜
     */
    private $city = '上海';
    // 经典网络 or VPC
    private $networkType = '经典网络';

    private $AccessKeyId = '';
    private $AccessKeySecret = '';
    private $host = '';
    private $ossClient;

    public function __construct($isInternal = false)
    {
        $this->AccessKeyId     = config("conf.aliyun.access_key_id");
        $this->AccessKeySecret = config('conf.aliyun.access_key_secret');
        $this->host            = config("conf.aliyun_oss.bucket_cdn");

        if ($this->networkType == 'VPC' && !$isInternal) {
            throw new Exception("VPC 网络下不提供外网上传、下载等功能");
        }
        $this->ossClient = AliyunOSS::boot(
            $this->city,
            $this->networkType,
            $isInternal,
            $this->AccessKeyId,
            $this->AccessKeySecret
        );
    }

    public function auth($pictureUri, $expire = 30)
    {

        $id   = $this->AccessKeyId;
        $key  = $this->AccessKeySecret;
        $host = $this->host;

        $dir  = $pictureUri["dir"]; // 用户上传文件时指定
        $name = $pictureUri["name"]; // 用户上传文件时指定
        $uri  = $pictureUri["uri"]; // 用户上传文件时指定

        $callbackUrl          = env("APP_URL") . "/media/media_image/save/oss_callback";
        $callback_param       = [
            'callbackUrl'      => $callbackUrl,
            'callbackBody'     => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => "application/x-www-form-urlencoded"];
        $callback_string      = json_encode($callback_param);
        $base64_callback_body = base64_encode($callback_string);
        $now                  = time();
        $end                  = $now + $expire;
        $expiration           = $this->gmt_iso8601($end);

        //最大文件大小.用户可以自己设置
        $condition    = [0 => 'content-length-range', 1 => 0, 2 => 1048576000];
        $conditions[] = $condition;
        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start        = [0 => 'starts-with', 1 => '$key', 2 => $dir];
        $conditions[] = $start;

        $arr            = ['expiration' => $expiration, 'conditions' => $conditions];
        $policy         = json_encode($arr);
        $base64_policy  = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature      = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response               = [];
        $response['accessid']   = $id;
        $response['host']       = $host;
        $response['policy']     = $base64_policy;
        $response['signature']  = $signature;
        $response['expire']     = $end;
        $response['expiration'] = $expiration;
        $response['callback']   = $base64_callback_body;
        $response['dir']        = $dir;  // 这个参数是设置用户上传文件时指定
        $response['name']       = $name;  // 这个参数是设置用户上传文件时指定
        $response['uri']        = $uri;  // 这个参数是设置用户上传文件时指定

        return json_decode(json_encode($response));
    }

    public function callBack()
    {
        //1.获取OSS的签名header和公钥url header
        $authorizationBase64 = "";
        $pubKeyUrlBase64     = "";
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorizationBase64 = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (isset($_SERVER['HTTP_X_OSS_PUB_KEY_URL'])) {
            $pubKeyUrlBase64 = $_SERVER['HTTP_X_OSS_PUB_KEY_URL'];
        }
        if ($authorizationBase64 == '' || $pubKeyUrlBase64 == '') {
            Log::debug(__METHOD__, ["error" => "header fail"]);
            exit("header fail");
        }

        // 2.获取OSS的签名
        $authorization = base64_decode($authorizationBase64);

        // 3.获取公钥
        $pubKeyUrl = base64_decode($pubKeyUrlBase64);
        $ch        = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pubKeyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $pubKey = curl_exec($ch);
        if ($pubKey == "") {
            Log::debug(__METHOD__, ["error" => "pubKey fail"]);
            exit("pubKey fail");
        }

        // 4.获取回调body
        $body = file_get_contents('php://input');
        Log::info(__METHOD__, json_decode($body));


        // 5.拼接待签名字符串
        $authStr = '';
        $path    = $_SERVER['REQUEST_URI'];
        $pos     = strpos($path, '?');
        if ($pos === false) {
            $authStr = urldecode($path) . "\n" . $body;
        } else {
            $authStr = urldecode(substr($path, 0, $pos)) . substr($path, $pos, strlen($path) - $pos) . "\n" . $body;
        }

        // 6.验证签名
        $ok = openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);
        if ($ok == 1) {
            $data = ["Status" => "Ok"];
            return json_encode($data);
        } else {
            return false;
        }
    }

    public function gmt_iso8601($time)
    {
        $dtStr      = date("c", $time);
        $datetime   = new \DateTime($dtStr);
        $expiration = $datetime->format(\DateTime::ISO8601);
        $pos        = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }

    // 获取私有文件的URL,过期时间为 1天
    public function privateUrl($bucketName, $ossKey)
    {
        $dateTime    = new DateTime();
        $expire_time = $dateTime->modify('+1 day');
        $exp         = config("conf.aliyun_oss.bucket") . "." . config("conf.aliyun_oss.base_host");

        $this->ossClient->setBucket($bucketName);
        $objectUrl = $this->ossClient->getUrl($ossKey, $expire_time);

        //oss https 开关
        if (config("conf.aliyun_oss.https")) {
            $objectUrl = preg_replace('/http:\\/\\//i', 'https://', $objectUrl);
        }
        //OSS CDN 开关
        if (config("conf.aliyun_oss.cdn")) {
            $objectUrl = preg_replace('/' . $exp . '/i', config("conf.aliyun_oss.bucket_cdn"), $objectUrl);
        }

        return $objectUrl;
    }

    // 获取私有文件的URL，并设定过期时间，如 \DateTime('+1 day')
    public static function getPrivateObjectURLWithExpireTime($bucketName, $ossKey, DateTime $expire_time)
    {
        $oss = new static();
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->getUrl($ossKey, $expire_time);
    }

    // 获取公开文件的 URL
    public static function getPublicObjectURL($bucketName, $ossKey)
    {
        $oss = new static();
        $oss->ossClient->setBucket($bucketName);
        return $oss->ossClient->getPublicUrl($ossKey);
    }


}