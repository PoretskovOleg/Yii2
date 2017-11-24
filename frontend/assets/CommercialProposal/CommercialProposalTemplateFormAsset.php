<?php

namespace frontend\assets\CommercialProposal;

use yii\web\AssetBundle;


class CommercialProposalTemplateFormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/commercial-proposal-template/form.css',
    ];
    public $js = [
        'js/commercial-proposal-template/form.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
