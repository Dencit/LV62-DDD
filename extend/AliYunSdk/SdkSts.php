<?php

namespace Extend\AliYunSdk;

use Modules\Base\Exception\Exception;
use Sts\Request\V20150401\AssumeRoleRequest as AssumeRole;
use DateTime;

class SdkSts
{

    protected $client;

    public function __construct()
    {
        if (empty($this->client)) {
            //引入阿里云核心sdk
            include_once(base_path('extend/AliYunSdk/core/Config.php'));
            $accessKey    = config("conf.aliyun.access_key_id");
            $accessSecret = config('conf.aliyun.access_key_secret');
            $ipRegionInfo = ['region_id' => 'cn-shanghai', 'endpoint' => 'sts.cn-shanghai.aliyuncs.com'];

            // 只允许子用户使用角色
            \DefaultProfile::addEndpoint($ipRegionInfo['region_id'], $ipRegionInfo['region_id'], "Sts", $ipRegionInfo['endpoint']);
            $iClientProfile = \DefaultProfile::getProfile($ipRegionInfo['region_id'], $accessKey, $accessSecret);
            $this->client   = new \DefaultAcsClient($iClientProfile);

        }
    }

    public function get($userId = 0)
    {

        // 角色资源描述符，在RAM的控制台的资源详情页上可以获取
        $roleArn = "acs:ram::1833699949394936:role/admin-user";
        // 在扮演角色(AssumeRole)时，可以附加一个授权策略，进一步限制角色的权限；
        // 详情请参考《RAM使用指南》
        /*        $policy=
        <<<POLICY
            {
              "Statement": [
                {
                  "Action": [
                    "oss:Get*",
                    "oss:List*"
                  ],
                  "Effect": "Allow",
                  "Resource": "*"
                }
              ],
              "Version": "1"
            }
        POLICY;*/

        $request = new AssumeRole();

        // RoleSessionName即临时身份的会话名称，用于区分不同的临时身份
        // 您可以使用您的客户的ID作为会话名称
        $request->setRoleSessionName("user_id_" . $userId);
        $request->setRoleArn($roleArn);
        //$request->setPolicy($policy);
        $request->setDurationSeconds(3600);


        try {
            $response = $this->client->getAcsResponse($request);
        } catch (\ServerException $e) {
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        } catch (\ClientException $e) {
            Exception::app($e->getErrorCode(), $e->getMessage(), __METHOD__);
        }


        return $response;

    }

    // 获取私有文件的URL，并设定过期时间，如 \DateTime('+1 day')
    protected static function getPrivateObjectUrl($bucketName, $ossKey)
    {
        $dateTime = new DateTime();
        $dateTime->modify('+1 day');
        $objectUrl = SdkOss::getPrivateObjectURLWithExpireTime($bucketName, $ossKey, $dateTime);
        return $objectUrl;
    }

    protected static function getPublicObjectUrl($bucketName, $ossKey)
    {
        $objectUrl = SdkOss::getPublicObjectURL($bucketName, $ossKey);
        return $objectUrl;
    }


}