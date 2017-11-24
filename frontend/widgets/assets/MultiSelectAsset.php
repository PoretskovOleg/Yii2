<?php

namespace frontend\widgets\assets;

use yii\web\AssetBundle;


class MultiSelectAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins/select2';
    public $css = [
        'select2.min.css',
    ];
}