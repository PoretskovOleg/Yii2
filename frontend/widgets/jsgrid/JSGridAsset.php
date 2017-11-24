<?php

namespace frontend\widgets\jsgrid;

use yii\web\AssetBundle;


class JSGridAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/jsgrid';
    public $css = [
        'css/jsgrid.css',
        'css/jsgrid-theme.css',
    ];
    public $js = [
        'js/jsgrid.js',
        'js/main.js',
    ];
}