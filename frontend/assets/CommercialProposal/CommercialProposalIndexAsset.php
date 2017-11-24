<?php

namespace frontend\assets\CommercialProposal;

use yii\web\AssetBundle;


class CommercialProposalIndexAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/commercial-proposal/index.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
