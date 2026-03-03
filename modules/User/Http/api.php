<?php

use Modules\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//开放权限
Route::prefix('user')->group(function () {

    //只对测试开放-正式接口不要放这里
    if (config('app.debug')) {
        //-批量新增
        //Route::post('/batch-in',AdminController::class.'@adminBatchCreate');
        //-批量更新
        //Route::put('/batch-up', AdminController::class.'@adminBatchUpdate');
    }

    //发送用户手机验证码
    Route::post('mobile_code/save', UserController::class . '@userMobileCodeSave');

    //用户-新增-注册
    Route::post('user/save', UserController::class . '@userSave');
    //用户-更新-登录
    Route::put('user/update', UserController::class . '@userUpdate');

});

//用户以上权限
Route::middleware('auth:user_auth,admin_auth,system_auth')->prefix('user')->group(function () {

    //用户-获取-自己的详情
    Route::get('user/read', UserController::class . '@userRead');
    //用户-更新-自己的详情
    Route::patch('user/patch', UserController::class . '@userPatch');

});

//管理以上权限
Route::middleware('auth:admin_auth,system_auth')->prefix('user')->group(function () {

    //管理员-获取-用户列表
    Route::get('user/index/adm', UserController::class . '@userIndexForAdm');
    //管理员-获取-用户详情
    Route::get('user/read/adm/{id}', UserController::class . '@userReadForAdm')->where(['id' => '\d+']);
    //管理员-更新-用户详情
    Route::patch('user/patch/adm/{id}', UserController::class . '@userPatchForAdm')->where(['id' => '\d+']);

});

//系统以上权限
Route::middleware('auth:system_auth')->prefix('user')->group(function () {

    //系统-删除-用户详情
    Route::delete('user/delete/sys/{id}', UserController::class . '@userDeleteForSys')->where(['id' => '\d+']);

});