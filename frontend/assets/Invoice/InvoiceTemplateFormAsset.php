<?php

namespace frontend\assets\Invoice;

use yii\web\AssetBundle;


class InvoiceTemplateFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/invoice-template/form.css',
    ];
    public $js = [
        'js/invoice-template/form.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
