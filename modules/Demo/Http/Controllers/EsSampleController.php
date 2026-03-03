<?php

namespace Modules\Demo\Http\Controllers;

use Modules\Demo\Http\Requests\EsSampleRequest;
use Modules\Demo\Srv\EsSampleSrv;
use Extend\Util\ApiCache;
use Illuminate\Http\Request;
use Modules\Base\Controller\BaseController;
use Modules\Base\Response\ApiTrans;
use Modules\Demo\Http\Trans\EsSampleTrans;

/**
 * notes: 数据单元控制器
 * Class EsSampleController
 * @package Modules\Demo\Http\Controllers
 */
class EsSampleController extends BaseController
{

    /*
     * -新增-索引库
     */
    public function esSampleTableSave(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();
        $validate     = new EsSampleRequest();
        $validate->checkSceneValidate('tableSave', $requestInput);

        //业务逻辑控制
        $result = (new EsSampleSrv())->esSampleTableSave($requestInput);

        //输出逻辑控制
        $result = ApiTrans::save($result);

        return ApiTrans::response($result);
    }

    /*
     * 新增数据 -
     */
    public function esSampleSave(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();
        $validate     = new EsSampleRequest();
        $validate->checkSceneValidate('save', $requestInput);

        //业务逻辑控制
        $result = (new EsSampleSrv())->esSampleSave($requestInput);

        //输出逻辑控制
        $result = ApiTrans::save($result);

        return ApiTrans::response($result);
    }

    /*
     * 批量新增数据 -
     */
    public function esSampleBatchSave(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();
        $rules        = [];
        $validate     = new EsSampleRequest();
        $this->batchDone(
            $requestInput, function ($item) use ($rules, $validate) {
            $this->arrayExcept($item, $rules);//数组排除输入字段
            $validate->checkSceneValidate('save', $item);
        }
        );

        //业务逻辑控制
        $result = (new EsSampleSrv())->esSampleBatchSave($requestInput);

        //输出逻辑控制
        $result = ApiTrans::batchSave($result);

        return ApiTrans::response($result);
    }

    /*
     * 根据 主键id 更新详情 - 
     */
    public function esSampleUpdate(Request $request, $id)
    {
        //输入逻辑控制
        $rules        = [];
        $requestInput = $request->except($rules);
        $validate     = new EsSampleRequest();
        $validate->checkSceneValidate('update', $requestInput);

        //业务逻辑控制
        $result = (new EsSampleSrv())->esSampleUpdate($id, $requestInput);

        //输出逻辑控制
        $result = ApiTrans::update($result);

        return ApiTrans::response($result);
    }

    /*
     * 根据 主键id 批量更新 - 
     */
    public function esSampleBatchUpdate(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();
        $rules        = [];
        $validate     = new EsSampleRequest();
        $this->batchDone(
            $requestInput, function ($item) use ($rules, $validate) {
            $this->arrayExcept($item, $rules);//数组排除输入字段
            $validate->checkSceneValidate('update', $item);
        }
        );

        //业务逻辑控制
        $result = (new EsSampleSrv())->esSampleBatchUpdate($requestInput);

        //输出逻辑控制
        $result = ApiTrans::batchUpdate($result);

        return ApiTrans::response($result);
    }


    /*
     * 列表筛选 - 
     */
    public function esSampleIndex(Request $request)
    {
        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        $result   = (new ApiCache)->collect(
            $hKey, $queryKey, function () use ($requestQuery) {

            //业务逻辑控制
            $result = (new EsSampleSrv())->esSampleIndex($requestQuery);

            //输出逻辑控制
            return ApiTrans::itemPageList($result, EsSampleTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);
    }


    /*
     * 根据 主键id 获取详情 - 
     */
    public function esSampleRead(Request $request, $id)
    {
        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        $result   = (new ApiCache)->collect(
            $hKey, $queryKey, function () use ($requestQuery, $id) {

            //业务逻辑控制
            $Service = new EsSampleSrv();
            $result  = $Service->esSampleRead($requestQuery, $id);

            //输出逻辑控制
            return ApiTrans::read($result, EsSampleTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);
    }

    /*
     * 根据 主键id 删除详情 - 
     */
    public function esSampleDelete($id)
    {

        //业务逻辑控制
        $Service = new EsSampleSrv();
        $result  = $Service->esSampleDelete($id);

        //输出逻辑控制
        $result = ApiTrans::delete($result);

        return ApiTrans::response($result);
    }


}
