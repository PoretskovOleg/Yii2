<?php

namespace frontend\assets\CommercialProposal;

use yii\web\AssetBundle;


class CommercialProposalFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/commercial-proposal/form.css',
    ];
    public $js = [
        'js/commercial-proposal/catalog_goods_model.js',
        'js/commercial-proposal/additional_goods_model.js',
        'js/commercial-proposal/totals_model.js',
        'js/commercial-proposal/goods_controller.js',
        'js/commercial-proposal/form.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
