<?php

namespace Modules\Media\Srv;

use Carbon\Carbon;
use DateTime;
use Extend\AliYunSdk\SdkOss;
use Modules\Base\Exception\Exception;
use Modules\Base\Srv\BaseSrv;
use Modules\Media\Enums\MediaImageEnum;
use Modules\Media\Errors\MediaRootError;
use Modules\Media\Models\MediaImageModel;

/**
 * notes: 领域层-业务类
 * desc: 当不同 应用端/模块 的 应用层-业务类,对同一个表数据(或第三方API)进行操作, 该表的操作代码分散在多个应用端中且冗余, 就需要抽象到这一层.
 * 领域层-业务类 允许 被 跨应用端/模块 调用, 而 各应用层-业务 则保持隔离, 避免应用层业务耦合.
 * 调用原则: 向下调用[仓储类,第三方服务-SDK]
 */
class MediaImageSrv extends BaseSrv
{

    //插入和更新时,检查关键数据.
    public static function checkData(&$requestInput)
    {

    }

    public static function picUri($requestInput)
    {

        switch ($requestInput['type']) {
            default :
                $baseKey = 'media/image/other/';
                break;
            case MediaImageEnum::AVATAR_IMAGE_TYPE:
                $baseKey = "media/image/avatar/";
                break;
            case MediaImageEnum::COVER_IMAGE_TYPE :
                $baseKey = "media/image/cover/";
                break;
        }

        $baseKey .= Carbon::now()->toDateString() . '/';

        //构造文件名
        $suffix    = $requestInput["suffix"];
        $imageName = self::udate("Ymd_His_u") . "." . $suffix;
        $ossKey    = $baseKey . $imageName;

        $result = [
            "dir"  => $baseKey,
            "name" => $imageName,
            "uri"  => $ossKey
        ];

        return $result;
    }

    public static function uploadPicture($requestInput)
    {

        switch ($requestInput['type']) {
            default :
                $baseKey = 'media/image/other';
                break;
            case MediaImageEnum::AVATAR_IMAGE_TYPE:
                $baseKey = "media/image/avatar/";
                break;
            case MediaImageEnum::COVER_IMAGE_TYPE :
                $baseKey = "media/image/cover/";
                break;
        }

        //判断图片类型 返回异常
        preg_match("/data:image\\/(jpg|jpeg|png|bmp|gif);/", $requestInput['base_64_data'], $m); //dd($m);
        if (!isset($m[1])) {
            Exception::app(MediaRootError::code("FORMAT_WRONG"), MediaRootError::msg("FORMAT_WRONG"), __METHOD__);
        }
        //构造文件名
        $suffix    = $m[1];
        $imageName = self::udate("Ymd_His_u") . "." . $suffix;
        $osskey    = $baseKey . Carbon::now()->toDateString() . '/' . $imageName; //dd($osskey);
        //判断Base64字节大小 返回异常
        $imageBase64 = explode(',', $requestInput['base_64_data'])[1];
        $strLength   = strlen(preg_replace("/=/", '', $imageBase64)); //dd($strLength);
        $fileLength  = (int)$strLength - ($strLength / 8) * 2; //dd($fileLength);
        if ($fileLength > config('media.image_max_size')) {
            Exception::app(MediaRootError::code("SIZE_EXCESS"), MediaRootError::msg("SIZE_EXCESS"), __METHOD__);
        }
        //解码base64
        $imageData = base64_decode($imageBase64);//dd($imageData);
        //上传到 oss-bucket
        $bool = OssService::publicUploadContent(config("conf.aliyun_oss.bucket"), $osskey, $imageData);
        //var_dump($bool->getETag());dd();

        $result = [];
        if ($bool) {
            //$requestInput['e-tag'] = $bool->getETag();
            //将数据存储数据库
            $query       = new MediaImageModel($requestInput);
            $query->host = config("conf.aliyun_oss.bucket");
            $query->uri  = $osskey;
            $query->saveOrFail();
            $result = $query->fresh();
        }

        return $result;
    }

    //生成微秒
    public static function udate($strFormat = 'u', $uTimeStamp = null)
    {
        // If the time wasn't provided then fill it in
        if (is_null($uTimeStamp)) {
            $uTimeStamp = microtime(true);
        }
        // Round the time down to the second
        $dtTimeStamp = floor($uTimeStamp);
        // Determine the millisecond value
        $intMilliseconds = round(($uTimeStamp - $dtTimeStamp) * 1000000);
        // Format the milliseconds as a 6 character string
        $strMilliseconds = str_pad($intMilliseconds, 6, '0', STR_PAD_LEFT);
        // Replace the milliseconds in the date format string
        // Then use the date function to process the rest of the string
        return date(preg_replace('`(?<!\\\\)u`', $strMilliseconds, $strFormat), $dtTimeStamp);
    }

    // 获取私有文件的URL，并设定过期时间，如 \DateTime('+1 day')
    public static function getPrivateObjectUrl($bucketName, $ossKey)
    {
        $dateTime = new DateTime();
        $dateTime->modify('+1 day');
        $objectUrl = SdkOss::getPrivateObjectURLWithExpireTime($bucketName, $ossKey, $dateTime);
        return $objectUrl;
    }

    public static function getPublicObjectUrl($bucketName, $ossKey)
    {
        $objectUrl = SdkOss::getPublicObjectURL($bucketName, $ossKey);
        return $objectUrl;
    }

}
