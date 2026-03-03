<?php

namespace Modules\User\Http\Controllers;

use Extend\AliYunSdk\SdkSms;
use Illuminate\Support\Facades\Redis;
use Modules\User\Http\Logics\UserLogic;
use Extend\Util\ApiCache;
use Illuminate\Http\Request;
use Modules\Base\Controller\BaseController;
use Modules\Base\Response\ApiTrans;
use Modules\User\Http\Trans\UserTrans;
use Modules\User\Http\Requests\UserRequest;

/**
 * notes: 应用层-控制器
 * 说明: 控制器内不写业务,只写http层面相关的逻辑,
 * 调用原则: 向下调用[输入验证类,业务类,输出转化类].
 */
class UserController extends BaseController
{

    //用户-发送-手机验证码
    public function userMobileCodeSave(Request $request)
    {
        //参数验证
        $rule = ['mobile' => 'required'];
        $this->validate($request, $rule);

        $mobile   = $request->get('mobile');
        $redisKey = config("cache.stores.redis.prefix") . $mobile;
        $code     = (100000 + rand(100000, 899999));

//        $sdkSms = new SdkSms();
//        $sdkSms->sendSms($mobile, ["code" => $code], config('conf.sms.code_tpl'));
//        if ($sdkSms) {
            //写入redis缓存
            $redis = Redis::connection();
            $redis->select(0);
            $redis->del($redisKey);
            $redis->select(1);
            $redis->set($redisKey, json_encode($code));
            $redis->expire($redisKey, 300);
//        }
        $result = ["code" => "OK"];

        //输出逻辑控制
        return ApiTrans::save($result, UserTrans::class, 'transform');
    }

    /*
     * 用户-新增-注册
     */
    public function userSave(Request $request)
    {
        //输入逻辑控制
        $requestInput = $request->post();
        $validate     = new UserRequest();
        $validate->checkSceneValidate('save', $requestInput);

        //业务逻辑控制
        $result = (new UserLogic())->userSave($requestInput);

        //输出逻辑控制
        $result = ApiTrans::save($result);

        return ApiTrans::response($result);
    }

    /*
     * 用户-更新-登录
     */
    public function userUpdate(Request $request)
    {
        //输入逻辑控制
        $rules        = ['id'];
        $requestInput = $request->except($rules);
        $validate     = new UserRequest();
        $validate->checkSceneValidate('update', $requestInput);

        //业务逻辑控制
        $result = (new UserLogic())->userUpdate($requestInput['mobile'], $requestInput);

        //输出逻辑控制
        $result = ApiTrans::update($result, UserTrans::class, 'transform');

        return ApiTrans::response($result);
    }

    /*
     * 用户-更新-自己的详情
     */
    public function userPatch(Request $request)
    {
        $userId = $this->auth('user_id');

        //输入逻辑控制
        $rules        = ['nickname', 'avatar', 'gender'];
        $requestInput = $request->only($rules);
        $validate     = new UserRequest();
        $validate->checkValidate($requestInput);

        //业务逻辑控制
        $result = (new UserLogic())->userPatch($userId, $requestInput);

        //输出逻辑控制
        $result = ApiTrans::update($result);

        return ApiTrans::response($result);
    }

    /*
     * 用户-获取-自己的详情
     */
    public function userRead(Request $request)
    {
        $userId = $this->auth('user_id');

        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        $queryKey .= '&user_id=' . $userId;
        $result   = (new ApiCache)->collect(
            $hKey, $queryKey, function () use ($requestQuery, $userId) {

            //业务逻辑控制
            $logic  = new UserLogic();
            $result = $logic->userRead($requestQuery, $userId);

            //输出逻辑控制
            return ApiTrans::read($result, UserTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);
    }

    /*
     * 管理员-获取-用户列表
     */
    public function userIndexForAdm(Request $request)
    {
        //$adminId = $this->auth('admin_id');

        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        //$queryKey .= '&admin_id=' . $adminId;
        $result = (new ApiCache)->collect(
            $hKey, $queryKey, function () use ($requestQuery) {

            //业务逻辑控制
            $result = (new UserLogic())->userIndexForAdm($requestQuery);

            //输出逻辑控制
            return ApiTrans::index($result, UserTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);

    }

    /*
     * 管理员-获取-用户详情
     */
    public function userReadForAdm(Request $request, $userId)
    {
        //$adminId = $this->auth('admin_id');

        //query string
        $requestQuery = $request->query();

        //api查询缓存
        $hKey     = ApiCache::makeHKeyByClassMethod(__CLASS__ . '@' . __FUNCTION__);
        $queryKey = ApiCache::makeQueryKeyByRequest($requestQuery);
        //$queryKey .= '&admin_id=' . $adminId;
        $result = (new ApiCache)->collect(
            $hKey, $queryKey, function () use ($requestQuery, $userId) {

            //业务逻辑控制
            $logic  = new UserLogic();
            $result = $logic->userReadForAdm($requestQuery, $userId);

            //输出逻辑控制
            return ApiTrans::read($result, UserTrans::class, 'transform');

        }, -1
        );

        return ApiTrans::response($result);
    }

    /*
     * 管理员-更新-用户详情
     */
    public function userPatchForAdm(Request $request, $userId)
    {
        //$adminId = $this->auth('admin_id');

        //输入逻辑控制
        $rules        = ['nickname', 'avatar', 'gender'];
        $requestInput = $request->only($rules);
        $validate     = new UserRequest();
        $validate->checkValidate($requestInput);

        //业务逻辑控制
        $result = (new UserLogic())->userPatch($userId, $requestInput);

        //输出逻辑控制
        $result = ApiTrans::update($result);

        return ApiTrans::response($result);
    }

    /*
     * 系统-删除-用户详情
     */
    public function userDeleteForSys($id)
    {

        //业务逻辑控制
        $logic  = new UserLogic();
        $result = $logic->userDeleteForSys($id);

        //输出逻辑控制
        $result = ApiTrans::delete($result);

        return ApiTrans::response($result);
    }

}
