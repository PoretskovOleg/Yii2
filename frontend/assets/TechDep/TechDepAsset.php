<?php

namespace frontend\assets\TechDep;

use yii\web\AssetBundle;

class TechDepAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/tech-dep/tech_dep.css'
    ];
    public $js = [
        'js/tech-dep/script.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset'
    ];
}
