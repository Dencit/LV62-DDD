<?php

namespace Modules\Oauth\Http\Controllers;

use Extend\Util\ApiCache;
use Illuminate\Http\Request;
use Modules\Base\Controller\BaseController;
use Modules\Base\Response\ApiTrans;
use Modules\Oauth\Http\Logics\OauthClientlogic;
use Modules\Oauth\Http\Trans\OauthClientTrans;
use Modules\Oauth\Http\Requests\OauthClientRequest;

/**
 * notes: 应用层-控制器
 * 说明: 控制器内不写业务,只写http层面相关的逻辑,
 * 调用原则: 向下调用[输入验证类,业务类,输出转化类].
 */
class OauthClientController extends BaseController
{


    /*
     * 新增数据 - 
     */
    public function oauthClientSave(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();

        $validate = new OauthClientRequest();
        $validate->checkSceneValidate('save', $requestInput);

        //业务逻辑控制
        $result = (new OauthClientlogic())->oauthClientSave($requestInput);

        //输出逻辑控制
        $result = ApiTrans::save($result);

        return ApiTrans::response($result);
    }


//{@block_cj}
//{@block_cj/}

//{@block_bc}
//{@block_bc/}


    /*
     * 根据 主键id 更新详情 - 
     */
    public function oauthClientUpdate(Request $request, $id)
    {
        //输入逻辑控制
        $rules        = [];
        $requestInput = $request->except($rules);
        $validate     = new OauthClientRequest();
        $validate->checkSceneValidate('update', $requestInput);

        //业务逻辑控制
        $result = (new OauthClientlogic())->oauthClientUpdate($id, $requestInput);

        //输出逻辑控制
        $result = ApiTrans::update($result);

        return ApiTrans::response($result);
    }


//{@block_bu}
//{@block_bu/}


    /*
     * 列表筛选 - 
     */
    public function oauthClientIndex(Request $request)
    {
        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        $result   = (new ApiCache)->collect(
            $hKey, $queryKey, function () use ($requestQuery) {

            //业务逻辑控制
            $result = (new OauthClientlogic())->oauthClientIndex($requestQuery);

            //输出逻辑控制
            return ApiTrans::index($result, OauthClientTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);
    }



    /*
     * 根据 主键id 获取详情 - 
     */
    public function oauthClientRead(Request $request, $id)
    {
        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        $result   = (new ApiCache)->collect(
            $hKey, $queryKey, function () use ($requestQuery, $id) {

            //业务逻辑控制
            $Service = new OauthClientlogic();
            $result  = $Service->oauthClientRead($requestQuery, $id);

            //输出逻辑控制
            return ApiTrans::read($result, OauthClientTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);
    }



    /*
     * 根据 主键id 删除详情 - 
     */
    public function oauthClientDelete(Request $request, $id)
    {
        //业务逻辑控制
        $Service = new OauthClientlogic();
        $result  = $Service->oauthClientDelete($id);

        //输出逻辑控制
        $result = ApiTrans::delete($result);

        return ApiTrans::response($result);
    }


}
