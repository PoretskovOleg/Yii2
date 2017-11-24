<?php

namespace frontend\assets\Invoice;

use yii\web\AssetBundle;


class InvoiceFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/invoice/form.css',
    ];
    public $js = [
        'js/invoice/catalog_goods_model.js',
        'js/invoice/additional_goods_model.js',
        'js/invoice/totals_model.js',
        'js/invoice/goods_controller.js',
        'js/invoice/form.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
