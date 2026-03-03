<?php

namespace Modules\Media\Http\Controllers;

use Carbon\Carbon;
use DateTime;
use Extend\AliYunSdk\SdkOss;
use Extend\Util\ApiCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Base\Controller\BaseController;
use Modules\Base\Response\ApiTrans;
use Modules\Media\Http\Logics\MediaImagelogic;
use Modules\Media\Http\Trans\MediaImageTrans;
use Modules\Media\Http\Requests\MediaImageRequest;
use Modules\Media\Srv\MediaImageSrv;

/**
 * notes: 应用层-控制器
 * 说明: 控制器内不写业务,只写http层面相关的逻辑,
 * 调用原则: 向下调用[输入验证类,业务类,输出转化类].
 */
class MediaImageController extends BaseController
{
    //新增_OSS_图片上传地址凭证
    public function saveForOssAuth(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();
        $validate     = new MediaImageRequest();
        $validate->checkSceneValidate('save_oss_auth', $requestInput);

        //业务逻辑控制
        $result = (new MediaImageLogic())->saveForOssAuth($requestInput);

        //输出逻辑控制
        $result = ApiTrans::save($result);

        return ApiTrans::response($result);
    }

    //新增_OSS_图片URI
    public function saveForOssUri(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();
        $validate     = new MediaImageRequest();
        $validate->checkSceneValidate('save_oss_uri', $requestInput);

        //业务逻辑控制
        $result = (new MediaImageLogic())->saveForOssUri($requestInput);

        //输出逻辑控制
        $result = ApiTrans::save($result, MediaImageTrans::class);

        return ApiTrans::response($result);
    }

    //新增_OSS_图片上传回调
    public function saveForOssCallBack(Request $request)
    {
        $OSS = new SdkOss();
        $OSS->callBack();
    }

//{@block_c}
//{@block_c/}

//{@block_cj}
//{@block_cj/}

//{@block_bc}
//{@block_bc/}

//{@block_u}
//{@block_u/}

//{@block_bu}
//{@block_bu/}

//{@block_br}
//{@block_br/}

//{@block_r}
//{@block_r/}

//{@block_d}
//{@block_d/}

}
