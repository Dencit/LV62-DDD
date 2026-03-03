<?php

namespace Modules\Admin\Http\Controllers;

use Extend\Util\ApiCache;
use Illuminate\Http\Request;
use Modules\Base\Controller\BaseController;
use Modules\Base\Response\ApiTrans;
use Modules\Admin\Http\Logics\Adminlogic;
use Modules\Admin\Http\Trans\AdminTrans;
use Modules\Admin\Http\Requests\AdminRequest;
use Modules\Admin\Errors\AdminRootError;
use Modules\Base\Exception\Exception;

/**
 * notes: 应用层-控制器
 * 说明: 控制器内不写业务,只写http层面相关的逻辑,
 * 调用原则: 向下调用[输入验证类,业务类,输出转化类].
 */
class AdminController extends BaseController
{

    /*
     * 管理-更新-登录
     */
    public function adminUpdate(Request $request)
    {
        //输入逻辑控制
        $rules        = ['id'];
        $requestInput = $request->except($rules);
        $validate     = new AdminRequest();
        $validate->checkSceneValidate('update', $requestInput);

        //业务逻辑控制
        $result = (new AdminLogic())->adminLogin($requestInput['mobile'], $requestInput);

        //输出逻辑控制
        $result = ApiTrans::update($result);

        return ApiTrans::response($result);
    }

    /*
     * 管理员-更新-自己的详情
     */
    public function adminPatch(Request $request)
    {
        $adminId = $this->auth('admin_id');

        //输入逻辑控制
        $rules        = ['name', 'avatar', 'gender'];
        $requestInput = $request->only($rules);
        $validate     = new AdminRequest();
        $validate->checkValidate($requestInput);

        //业务逻辑控制
        $result = (new AdminLogic())->adminMeUpdate($adminId, $requestInput);

        //输出逻辑控制
        $result = ApiTrans::update($result);

        return ApiTrans::response($result);
    }

    /*
     * 管理-获取-自己的信息
     */
    public function adminRead(Request $request)
    {
        $adminId = $this->auth('admin_id');

        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        $queryKey .= '&admin_id=' . $adminId;
        $result   = (new ApiCache())->collect(
            $hKey, $queryKey, function () use ($requestQuery, $adminId) {

            //业务逻辑控制
            $logic  = new AdminLogic();
            $result = $logic->adminMeDetail($requestQuery, $adminId);

            //输出逻辑控制
            return ApiTrans::read($result, AdminTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);
    }

    /*
     * 系统-新增-管理员
     */
    public function adminSaveForSys()
    {
        //$adminId = $this->auth('admin_id');

        //输入逻辑控制
        $rules        = ['name', 'mobile', 'password'];
        $requestInput = request()->only($rules);
        $validate     = new AdminRequest();
        $validate->checkSceneValidate('save', $requestInput);

        //业务逻辑控制
        $result = (new AdminLogic())->adminSysCreate($requestInput);

        //输出逻辑控制
        $result = ApiTrans::save($result);

        return ApiTrans::response($result);
    }

    /*
     * 系统-获取-管理员列表
     */
    public function adminIndexForSys(Request $request)
    {
        //$adminId = $this->auth('admin_id');

        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        $result   = (new ApiCache)->collect(
            $hKey, $queryKey, function () use ($requestQuery) {

            //业务逻辑控制
            $result = (new AdminLogic())->adminIndexForSys($requestQuery);

            //输出逻辑控制
            return ApiTrans::index($result, AdminTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);
    }

    /*
     * 系统-获取-管理员详情
     */
    public function adminReadForSys(Request $request)
    {
        $adminId = $this->auth('admin_id');

        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        $queryKey .= '&admin_id=' . $adminId;
        $result   = (new ApiCache)->collect(
            $hKey, $queryKey, function () use ($requestQuery, $adminId) {

            //业务逻辑控制
            $logic  = new AdminLogic();
            $result = $logic->adminSysDetail($requestQuery, $adminId);

            //输出逻辑控制
            return ApiTrans::read($result, AdminTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);
    }

    /*
     * 系统-更新-管理员详情
     */
    public function adminPatchForSys(Request $request, $adminId)
    {
        //$adminId = $this->auth('admin_id');

        //输入逻辑控制
        $rules        = ['name', 'avatar', 'gender', 'role', 'status'];
        $requestInput = $request->only($rules);
        $validate     = new AdminRequest();
        $validate->checkSceneValidate('patch', $requestInput);

        //业务逻辑控制
        $result = (new AdminLogic())->adminSysUpdate($adminId, $requestInput);

        //输出逻辑控制
        $result = ApiTrans::update($result);

        return ApiTrans::response($result);
    }

    /*
     * 系统-删除-管理员信息
     */
    public function adminDeleteForSys(Request $request, $id)
    {
        $adminId = $this->auth('admin_id');

        //不能自己删除自己
        if ($adminId == $id) {
            Exception::app(AdminRootError::code("DON_DELETE_YOU_SELF"), AdminRootError::msg("DON_DELETE_YOU_SELF"), __METHOD__);
        }

        //业务逻辑控制
        $logic  = new AdminLogic();
        $result = $logic->adminSysDelete($id);

        //输出逻辑控制
        $result = ApiTrans::delete($result);

        return ApiTrans::response($result);
    }

}
