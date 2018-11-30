<?php

use Encore\AliOssUpload\Http\Controllers\AliOssUploadController;

// 获取Alioss参数
Route::get('alioss-upload_policy', AliOssUploadController::class.'@index');

// 删除
Route::get('alioss-upload_del', AliOssUploadController::class.'@delete');