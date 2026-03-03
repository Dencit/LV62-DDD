<?php

use Modules\Oauth\Http\Controllers\OauthClientController;
use Modules\Oauth\Http\Controllers\OauthRoleController;
use Illuminate\Support\Facades\Route;

//开放权限
Route::prefix('oauth')->group(function(){

    //只对测试开放-正式接口不要放这里
    if(config('app.debug')) {
/*
        //-新增队列
        Route::post('/client/job-save', OauthClientController::class . '@oauthClientJobSave');
        //-批量新增
        Route::post('/client/batch-save', OauthClientController::class . '@oauthClientBatchSave');
        //-批量更新
        Route::put('/client/batch-update', OauthClientController::class . '@oauthClientBatchUpdate');
*/

    }

});


//用户以上权限
Route::middleware('auth:user_auth,admin_auth,system_auth')->prefix('oauth')->group(
    function () {

    }
);

//管理以上权限
Route::middleware('auth:admin_auth,system_auth')->prefix('oauth')->group(
    function () {

    }
);

//系统以上权限
Route::middleware('auth:system_auth')->prefix('oauth')->group(
    function () {

        //系统-新增-授权客户端
        Route::post('/oauth_client/save', OauthClientController::class . '@oauthClientSave');
        //系统-获取-授权客户端-列表
        Route::get('/oauth_client/index', OauthClientController::class . '@oauthClientIndex');
        //系统-获取-授权客户端-详情
        Route::get('/oauth_client/read/{id}', OauthClientController::class . '@oauthClientRead')->where(['id' => '\d+']);
        //系统-更新-授权客户端
        Route::put('/oauth_client/update/{id}', OauthClientController::class . '@oauthClientUpdate')->where(['id' => '\d+']);
        //系统-删除-授权客户端
        Route::delete('/oauth_client/delete/{id}', OauthClientController::class . '@oauthClientDelete')->where(['id' => '\d+']);

        //系统-新增-授权角色
        Route::post('/oauth_role/save', OauthRoleController::class . '@oauthRoleSave');
        //系统-获取-授权角色-列表
        Route::get('/oauth_role/index', OauthRoleController::class . '@oauthRoleIndex');
        //系统-获取-授权角色-详情
        Route::get('/oauth_role/read/{id}', OauthRoleController::class . '@oauthRoleRead')->where(['id' => '\d+']);
        //系统-更新-授权角色
        Route::put('/oauth_role/update/{id}', OauthRoleController::class . '@oauthRoleUpdate')->where(['id' => '\d+']);
        //系统-删除-授权角色
        Route::delete('/oauth_role/delete/{id}', OauthRoleController::class . '@oauthRoleDelete')->where(['id' => '\d+']);


    }
);