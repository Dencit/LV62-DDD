<?php

namespace Modules\Demo\Http\Controllers;

use Extend\Util\ApiCache;
use Illuminate\Http\Request;
use Modules\Base\Controller\BaseController;
use Modules\Base\Response\ApiTrans;
use Modules\Demo\Http\Logics\Samplelogic;
use Modules\Demo\Http\Trans\SampleTrans;
use Modules\Demo\Http\Requests\SampleRequest;

/**
 * notes: 应用层-控制器
 * 说明: 控制器内不写业务,只写http层面相关的逻辑,
 * 调用原则: 向下调用[输入验证类,业务类,输出转化类].
 */
class SampleController extends BaseController
{

//{@block_c}
    /*
     * 新增数据 - 模板
     */
    public function sampleSave(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();

        $validate = new SampleRequest();
        $validate->checkSceneValidate('save', $requestInput);

        //业务逻辑控制
        $result = (new Samplelogic())->sampleSave($requestInput);

        //输出逻辑控制
        $result = ApiTrans::save($result);

        return ApiTrans::response($result);
    }
//{@block_c/}

//{@block_cj}
    /*
     * 新增队列数据 - 模板
     */
    public function sampleJobSave(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();

        $validate = new SampleRequest();
        $validate->checkSceneValidate('save', $requestInput);

        //业务逻辑控制
        $result = (new Samplelogic())->sampleJobSave($requestInput);

        //输出逻辑控制
        $result = ApiTrans::save($result);

        return ApiTrans::response($result);
    }
//{@block_cj/}

//{@block_bc}
    /*
     * 批量新增数据 - 模板
     */
    public function sampleBatchSave(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();
        $rules        = [];
        $validate     = new SampleRequest();
        $this->batchDone(
            $requestInput, function ($item) use ($rules, $validate) {
            $this->arrayExcept($item, $rules);//数组排除输入字段
            $validate->checkSceneValidate('save', $item);
        }
        );

        //业务逻辑控制
        $result = (new Samplelogic())->sampleBatchSave($requestInput);

        //输出逻辑控制
        $result = ApiTrans::batchSave($result);

        return ApiTrans::response($result);
    }
//{@block_bc/}

//{@block_u}
    /*
     * 根据 主键id 更新详情 - 模板
     */
    public function sampleUpdate(Request $request, $id)
    {
        //输入逻辑控制
        $rules        = [];
        $requestInput = $request->except($rules);
        $validate     = new SampleRequest();
        $validate->checkSceneValidate('update', $requestInput);

        //业务逻辑控制
        $result = (new Samplelogic())->sampleUpdate($id, $requestInput);

        //输出逻辑控制
        $result = ApiTrans::update($result);

        return ApiTrans::response($result);
    }
//{@block_u/}

//{@block_bu}
    /*
     * 根据 主键id 批量更新 - 模板
     */
    public function sampleBatchUpdate(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();
        $rules        = [];
        $validate     = new SampleRequest();
        $this->batchDone(
            $requestInput, function ($item) use ($rules, $validate) {
            $this->arrayExcept($item, $rules);//数组排除输入字段
            $validate->checkSceneValidate('update', $item);
        }
        );

        //业务逻辑控制
        $result = (new Samplelogic())->sampleBatchUpdate($requestInput);

        //输出逻辑控制
        $result = ApiTrans::batchUpdate($result);

        return ApiTrans::response($result);
    }
//{@block_bu/}

//{@block_br}
    /*
     * 列表筛选 - 模板
     */
    public function sampleIndex(Request $request)
    {
        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        $result   = (new ApiCache)->collect(
            $hKey, $queryKey, function () use ($requestQuery) {

            //业务逻辑控制
            $result = (new Samplelogic())->sampleIndex($requestQuery);

            //输出逻辑控制
            return ApiTrans::index($result, SampleTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);
    }
//{@block_br/}

//{@block_r}
    /*
     * 根据 主键id 获取详情 - 模板
     */
    public function sampleRead(Request $request, $id)
    {
        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        $result   = (new ApiCache)->collect(
            $hKey, $queryKey, function () use ($requestQuery, $id) {

            //业务逻辑控制
            $Service = new Samplelogic();
            $result  = $Service->sampleRead($requestQuery, $id);

            //输出逻辑控制
            return ApiTrans::read($result, SampleTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);
    }
//{@block_r/}

//{@block_d}
    /*
     * 根据 主键id 删除详情 - 模板
     */
    public function sampleDelete(Request $request, $id)
    {
        //业务逻辑控制
        $Service = new Samplelogic();
        $result  = $Service->sampleDelete($id);

        //输出逻辑控制
        $result = ApiTrans::delete($result);

        return ApiTrans::response($result);
    }
//{@block_d/}

}
