<?php

namespace Extend\AliYunSdk;

use Carbon\Carbon;
use live\Request\V20161101\DescribeLiveStreamRecordContentRequest as RecordContent;
use live\Request\V20161101\DescribeLiveStreamRecordIndexFilesRequest as RecordIndexFiles;
use live\Request\V20161101\DescribeLiveStreamRecordIndexFileRequest as RecordIndexFile;
use live\Request\V20161101\DescribeLiveStreamSnapshotInfoRequest as SnapshotInfo;
use live\Request\V20161101\DescribeLiveSnapshotConfigRequest as SnapshotConfig;
use DateTime;

class SdkLive
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
            $accessKey      = config("conf.aliyun.access_key_id");
            $accessSecret   = config('conf.aliyun.access_key_secret');
            $ipRegionInfo   = ['region_id' => 'cn-shanghai', 'endpoint' => 'live.cn-shanghai.aliyuncs.com'];
            $iClientProfile = \DefaultProfile::getProfile($ipRegionInfo['region_id'], $accessKey, $accessSecret);
            //根据地区信息添加距离该客户端ip位置的主机服务器,加快访问速度
            \DefaultProfile::addEndpoint($ipRegionInfo['region_id'], $ipRegionInfo['region_id'], "Live", $ipRegionInfo['endpoint']);
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

    //查询录制内容
    public function getRecordContent($queryArr)
    {

        $startTime              = $queryArr['start_time'];
        $queryArr['start_time'] = Carbon::parse($startTime)->toIso8601ZuluString();
        $queryArr['end_time']   = Carbon::parse($startTime)->addDays(4)->toIso8601ZuluString();

        $request = new RecordContent();
        $request->setAppName($this->appName);
        $request->setDomainName($this->liveVhost);
        $request->setStreamName($queryArr['stream_name']);
        $request->setStartTime($queryArr['start_time']);
        $request->setEndTime($queryArr['end_time']);
        $response = $this->client->getAcsResponse($request);

        //
        if (!empty($response->RecordContentInfoList)) {
            $recordContentInfos = $response->RecordContentInfoList->RecordContentInfo;
            return $recordContentInfos;
        }

        return false;
    }

    //查询录制索引文件
    public function getRecordIndexFiles($queryArr, $quality = "_ld")
    {

        $startTime              = $queryArr['start_time'];
        $queryArr['start_time'] = Carbon::parse($startTime)->toIso8601ZuluString();
        $queryArr['end_time']   = Carbon::parse($startTime)->addDays(4)->toIso8601ZuluString();

        $request = new RecordIndexFiles();
        $request->setAppName($this->appName);
        $request->setDomainName($this->liveVhost);
        $request->setStreamName($queryArr['stream_name']);
        $request->setStartTime($queryArr['start_time']);
        $request->setEndTime($queryArr['end_time']);
        $response = $this->client->getAcsResponse($request);

        //合成完整连接
        if (!empty($response->RecordIndexInfoList)) {
            $recordIndexInfos = $response->RecordIndexInfoList->RecordIndexInfo;
            //dd($recordIndexInfos);
            $info = [];
            $exp  = config("conf.aliyun_oss.mps_bucket") . "." . config("conf.aliyun_oss.base_host");
            //dd($exp);
            foreach ($recordIndexInfos as $k => $v) {
                //媒体转码,强制转到另一个 bucket 里，路径相同，修改一下就可以。
                $v->OssObject = preg_replace("/\\.m3u8$/", $quality . ".mp4", $v->OssObject);
                $info[$k]     = self::getPrivateObjectUrl(config("conf.aliyun_oss.mps_bucket"), $v->OssObject);
                //oss https 开关
                if (config("conf.aliyun_oss.https")) {
                    $info[$k] = preg_replace('/http:\\/\\//i', 'https://', $info[$k]);
                }
                //OSS CDN 开关
                if (config("conf.aliyun_oss.cdn")) {
                    $info[$k] = preg_replace('/' . $exp . '/i', config("conf.aliyun_oss.mps_bucket_cdn"), $info[$k]);
                }
            }
            return $info;
        }

        return false;
    }

    //查询单个录制索引文件
    public function getRecordIndexFile($queryArr)
    {

        $request = new RecordIndexFile();
        $request->setAppName($this->appName);
        $request->setDomainName($this->liveVhost);
        $request->setRecordId($queryArr['record_id']);
        $request->setStreamName($queryArr['stream_name']);
        $response = $this->client->getAcsResponse($request);

        if (!empty($response->RecordIndexInfo)) return $response->RecordIndexInfo;

        return false;

    }

    //查询截图信息
    public function getSnapshotInfo($queryArr)
    {

        $startTime              = $queryArr['start_time'];
        $queryArr['start_time'] = Carbon::parse($startTime)->toIso8601ZuluString();
        $queryArr['end_time']   = Carbon::parse($startTime)->addDays(1)->toIso8601ZuluString();

        $request = new SnapshotInfo();
        $request->setAppName($this->appName);
        $request->setDomainName($this->liveVhost);
        $request->setStreamName($queryArr['stream_name']);
        $request->setLimit(100);
        $request->setStartTime($queryArr['start_time']);
        $request->setEndTime($queryArr['end_time']);
        $response = $this->client->getAcsResponse($request);
        //dd($response->LiveStreamSnapshotInfoList->LiveStreamSnapshotInfo);

        //合成完整连接
        if (!empty($response->LiveStreamSnapshotInfoList)) {
            $snapshotInfos = $response->LiveStreamSnapshotInfoList->LiveStreamSnapshotInfo;
            $info          = [];
            $exp           = config("conf.aliyun_oss.bucket") . "." . config("conf.aliyun_oss.base_host");
            //dd($exp);
            foreach ($snapshotInfos as $k => $v) {
                $info[$k] = self::getPrivateObjectUrl($v->OssBucket, $v->OssObject . config("conf.aliyun_oss.pic_normal"));
                //oss https 开关
                if (config("conf.aliyun_oss.https")) {
                    $info[$k] = preg_replace('/http:\\/\\//i', 'https://', $info[$k]);
                }
                //OSS CDN 开关
                if (config("conf.aliyun_oss.cdn")) {
                    $info[$k] = preg_replace('/' . $exp . '/i', config("conf.aliyun_oss.bucket_cdn"), $info[$k]);
                }
            }

            $ossObject = preg_replace('/(\\/\d+\\.)/i', '.', $snapshotInfos[0]->OssObject);
            //dd($ossObject);
            $cover = self::getPrivateObjectUrl($snapshotInfos[0]->OssBucket, $ossObject . config("conf.aliyun_oss.pic_normal"));
            //oss https 开关
            if (config("conf.aliyun_oss.https")) {
                $cover = preg_replace('/http:\\/\\//i', 'https://', $cover);
            }
            //OSS CDN 开关
            if (config("conf.aliyun_oss.cdn")) {
                $cover = preg_replace('/' . $exp . '/i', config("conf.aliyun_oss.bucket_cdn"), $cover);
            }
            array_unshift($info, $cover);
            return $info;
        }

        return false;
    }

    //查询域名下的截图配置
    public function getSnapshotConfig()
    {

        $request = new SnapshotConfig();
        $request->setAppName($this->appName);
        $request->setDomainName($this->liveVhost);
        $response = $this->client->getAcsResponse($request);

        dd($response);

        if (!empty($response->RecordIndexInfo)) return $response->RecordIndexInfo;

        return false;
    }


}