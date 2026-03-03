<?php

use Modules\Demo\Http\Controllers\EsSampleController;
use Modules\Demo\Http\Controllers\SampleController;
use Illuminate\Support\Facades\Route;

//开放权限
Route::prefix('demo')->group(function(){

    //只对测试开放-正式接口不要放这里
    if(config('app.debug')) {

        //-新增
        Route::post('/sample/save', SampleController::class . '@sampleSave');
        //-新增队列
        Route::post('/sample/job_save', SampleController::class . '@sampleJobSave');
        //-获取-列表
        Route::get('/sample/index', SampleController::class . '@sampleIndex');
        //-获取-详情
        Route::get('/sample/read/{id}', SampleController::class . '@sampleRead')->where(['id' => '\d+']);
        //-更新-详情
        Route::put('/sample/update/{id}', SampleController::class . '@sampleUpdate')->where(['id' => '\d+']);
        //-删除-详情
        Route::delete('/sample/delete/{id}', SampleController::class . '@sampleDelete')->where(['id' => '\d+']);
        //-批量新增
        Route::post('/sample/batch_save', SampleController::class . '@sampleBatchSave');
        //-批量更新
        Route::put('/sample/batch_update', SampleController::class . '@sampleBatchUpdate');


        //-ES新增索引库
        Route::post('/es_sample/table/save', EsSampleController::class . '@esSampleTableSave');
        //-ES新增
        Route::post('/es_sample/save', EsSampleController::class . '@esSampleSave');
        //-ES获取-列表
        Route::get('/es_sample/index', EsSampleController::class . '@esSampleIndex');
        //-ES获取-详情
        Route::get('/es_sample/read/{id}', EsSampleController::class . '@esSampleRead')->where(['id' => '\d+']);
        //-ES更新-详情
        Route::put('/es_sample/update/{id}', EsSampleController::class . '@esSampleUpdate')->where(['id' => '\d+']);
        //-ES删除-详情
        Route::delete('/es_sample/delete/{id}', EsSampleController::class . '@esSampleDelete')->where(['id' => '\d+']);
        //-ES批量新增
        Route::post('/es_sample/batch_save', EsSampleController::class . '@esSampleBatchSave');
        //-ES批量更新
        Route::put('/es_sample/batch_update', EsSampleController::class . '@esSampleBatchUpdate');

    }

});


//用户以上权限
Route::middleware('auth:user_auth,admin_auth,system_auth')->prefix('demo')->group(
    function () {

    }
);

//管理以上权限
Route::middleware('auth:admin_auth,system_auth')->prefix('demo')->group(
    function () {

    }
);

//系统以上权限
Route::middleware('auth:system_auth')->prefix('demo')->group(
    function () {

    }
);