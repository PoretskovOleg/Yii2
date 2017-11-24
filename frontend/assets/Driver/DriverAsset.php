<?php

namespace frontend\assets\Driver;

use yii\web\AssetBundle;

class DriverAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/driver/driver.css'
    ];
    public $js = [
        'js/libs/jquery.maskedinput.js',
        'js/driver/script.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset'
    ];
}
