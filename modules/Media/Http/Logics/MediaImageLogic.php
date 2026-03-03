<?php

namespace Modules\Media\Http\Logics;

use Extend\AliYunSdk\SdkOss;
use Modules\Base\Logic\BaseLogic;
use Modules\Media\Reposit\MediaImageRepo;
use Modules\Media\Srv\MediaImageSrv;

/**
 * notes: 应用层-业务类
 * 说明: 业务类数据操作,一般不直接调用模型,通过仓储类提供存粹的数据执行函数, 跨 应用端/模块 操作同一数据类型的业务, 建议抽象到 领域层-业务类, 减少冗余.
 * 调用原则: 向下调用[仓储类,领域层-业务类]
 */
class MediaImageLogic extends BaseLogic
{
    //新增_OSS_图片上传地址凭证
    public function saveForOssAuth($requestInput)
    {
        $pictureUri = MediaImageSrv::picUri($requestInput);
        $expire     = 1;
        if (isset($requestInput['expire']) && !empty($requestInput['expire'])) {
            $expire = $requestInput['expire'];
        }

        $OSS    = new SdkOss();
        $result = $OSS->auth($pictureUri, $expire);
        $result = json_decode(json_encode($result), true);

        return $result;
    }

    //新增_OSS_图片URI
    public function saveForOssUri($requestInput)
    {
        //将数据存储数据库
        $requestInput["host"] = config("conf.aliyun_oss.bucket");

        $builder = MediaImageRepo::newInstance($requestInput);
        $builder->saveOrFail();
        $result = $builder->fresh();
        return $result;
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

//{@block_cmd}
//{@block_cmd/}

}
