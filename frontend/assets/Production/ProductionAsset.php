<?php

namespace frontend\assets\Production;

use yii\web\AssetBundle;

class ProductionAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/production/style.css'
    ];
    public $js = [
        'js/production/script.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset'
    ];
}