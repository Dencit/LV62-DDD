<?php

use Modules\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

//开放权限
Route::prefix('admin')->group(function () {

    //只对测试开放-正式接口不要放这里
    if (config('app.debug')) {
        //-批量新增
        //Route::post('/batch-in',AdminController::class.'@adminBatchCreate');
        //-批量更新
        //Route::put('/batch-up', AdminController::class.'@adminBatchUpdate');
    }

    //管理-更新-登录
    Route::put('admin/update', AdminController::class . '@adminUpdate');

});


//用户以上权限
Route::middleware('auth:user_auth,admin_auth,system_auth')->prefix('admin')->group(function () {

});

//管理以上权限
Route::middleware('auth:admin_auth,system_auth')->prefix('admin')->group(function () {

    //管理-获取-自己的信息
    Route::get('admin/read', AdminController::class . '@adminRead');
    //管理员-更新-自己的详情
    Route::patch('admin/patch', AdminController::class . '@adminPatch');

});

//系统以上权限
Route::middleware('auth:system_auth')->prefix('admin')->group(function () {

    //系统-新增-管理员
    Route::post('admin/save/sys', AdminController::class . '@adminSaveForSys');
    //系统-获取-管理员列表
    Route::get('admin/index/sys', AdminController::class . '@adminIndexForSys');
    //系统-获取-管理员详情
    Route::get('admin/read/sys/{id}', AdminController::class . '@adminReadForSys')->where(['id' => '\d+']);
    //系统-更新-管理员详情
    Route::patch('admin/patch/sys/{id}', AdminController::class . '@adminPatchForSys');
    //系统-删除-管理员信息
    Route::delete('admin/delete/sys/{id}', AdminController::class . '@adminDeleteForSys')->where(['id' => '\d+']);

});