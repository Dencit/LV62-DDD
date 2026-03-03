<?php

namespace Extend\AliYunSdk;

use Mts\Request\V20140618\QueryMediaListByURLRequest as QueryMediaListByURL;
use Mts\Request\V20140618\QueryMediaListRequest as QueryMediaList;
use DateTime;

class SdkMts
{

    protected $appName;
    protected $liveVhost;
    protected $client;

    public function __construct()
    {
        $this->appName   = config("conf.aliyun_live.app_name");
        $this->liveVhost = config("conf.aliyun_live.vhost");
        if (empty($this->client)) {
            //引入阿里云核心sdk
            include_once(base_path('extend/AliYunSdk/core/Config.php'));
            $accessKey    = config("conf.aliyun.access_key_id");
            $accessSecret = config('conf.aliyun.access_key_secret');
            //存在ip地址则算出所在地理位置
            $ipRegionInfo   = ['region' => 'cn-hangzhou', 'region_host' => 'mts.aliyuncs.com'];
            $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $accessKey, $accessSecret);
            //根据地区信息添加距离该客户端ip位置的主机服务器,加快访问速度
            \DefaultProfile::addEndpoint($ipRegionInfo['region'], $ipRegionInfo['region'], "Mts", $ipRegionInfo['region_host']);
            $this->client = new \DefaultAcsClient($iClientProfile);
        }
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

    public function getQueryMediaListByURL($pubUrl)
    {

        $request = new QueryMediaListByURL();
        $request->setFileURLs($pubUrl);
        $request->setIncludePlayList(true);
        $request->setIncludeSnapshotList(true);
        $request->setIncludeMediaInfo(true);

        $response = $this->client->getAcsResponse($request);

        return false;
    }

    public function getQueryMediaList($mediaIds)
    {

        $request = new QueryMediaList();
        $request->setMediaIds("0f9b590f4af04092818df691e1e715b9");
        /*$request->setIncludePlayList(true);
        $request->setIncludeSnapshotList(true);
        $request->setIncludeMediaInfo(true);*/

        $response = $this->client->getAcsResponse($request);

        dd($response);

        return false;
    }

}