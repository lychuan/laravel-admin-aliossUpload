<?php

namespace Encore\AliOssUpload;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Illuminate\Support\ServiceProvider;

class AliOssUploadServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(AliOssUpload $extension)
    {
        if (! AliOssUpload::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'alioss-upload');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-admin-ext/alioss-upload')],
                'alioss-upload'
            );
        }

        Admin::booting(function () {
            Form::extend('aliOss', AliOss::class);
        });

        $this->app->booted(function () {
            AliOssUpload::routes(__DIR__.'/../routes/web.php');
        });
    }
}