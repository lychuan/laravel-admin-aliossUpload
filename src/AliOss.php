<?php
namespace Encore\AliOssUpload;
use Encore\Admin\Form\Field;
class AliOss extends Field
{
    protected $view = 'alioss-upload::index';
    protected static $css = [
        'vendor/laravel-admin-ext/alioss-upload/style.css',
    ];
    protected static $js = [
        'vendor/laravel-admin-ext/alioss-upload/Sortable.min.js',
        'vendor/laravel-admin-ext/alioss-upload/plupload/plupload.full.min.js',
        'vendor/laravel-admin-ext/alioss-upload/plupload/i18n/zh_CN.js',
        'vendor/laravel-admin-ext/alioss-upload/alioss.js',
    ];
    public function render()
    {
        $name = $this->formatName($this->column);
        $token = csrf_token();
        $this->script = <<<EOT
alioss_upload('{$name}','$token');
EOT;
        return parent::render();
    }
}