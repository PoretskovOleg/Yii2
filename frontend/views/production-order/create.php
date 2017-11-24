<?php

use yii\helpers\Html;
use frontend\assets\Production\ProductionAsset;

ProductionAsset::register($this);

$this->title = 'Создать заказ-наряд';
$this->params['breadcrumbs'][] = ['label' => 'Реестр заказ-нарядов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Заказ-наряд';
?>
<div class="production-order-create">

    <?= $this->render('_form', [
        'model' => $model,
        'goodSearchModel' => $goodSearchModel,
        'stages' => array()
    ]) ?>

</div>
