<?php

use Modules\Media\Http\Controllers\MediaImageController;
use Illuminate\Support\Facades\Route;

//开放权限
Route::prefix('media')->group(function () {

    //只对测试开放-正式接口不要放这里
    if (config('app.debug')) {

//        //-新增
//        Route::post('/image/save', MediaImageController::class . '@mediaImageSave');
//        //-新增队列
//        Route::post('/image/job_save', MediaImageController::class . '@mediaImageJobSave');
//        //-获取-列表
//        Route::get('/image/index', MediaImageController::class . '@mediaImageIndex');
//        //-获取-详情
//        Route::get('/image/read/{id}', MediaImageController::class . '@mediaImageRead')->where(['id' => '\d+']);
//        //-更新-详情
//        Route::put('/image/update/{id}', MediaImageController::class . '@mediaImageUpdate')->where(['id' => '\d+']);
//        //-删除-详情
//        Route::delete('/image/delete/{id}', MediaImageController::class . '@mediaImageDelete')->where(['id' => '\d+']);
//        //-批量新增
//        Route::post('/image/batch_save', MediaImageController::class . '@mediaImageBatchSave');
//        //-批量更新
//        Route::put('/image/batch_update', MediaImageController::class . '@mediaImageBatchUpdate');

    }

    //新增_OSS_图片上传回调
    Route::post('/media_image/save/oss_callback', MediaImageController::class . '@saveForOssCallBack');

});


//用户以上权限
Route::middleware('auth:user_auth,admin_auth,system_auth')->prefix('media')->group(function () {
    //新增_OSS_图片上传地址凭证
    Route::post('/media_image/save/oss_auth', MediaImageController::class . '@saveForOssAuth');
    //新增_OSS_图片URI
    Route::post('/media_image/save/oss_uri', MediaImageController::class . '@saveForOssUri');
});

//管理以上权限
Route::middleware('auth:admin_auth,system_auth')->prefix('media')->group(
    function () {

    }
);

//系统以上权限
Route::middleware('auth:system_auth')->prefix('media')->group(
    function () {

    }
);