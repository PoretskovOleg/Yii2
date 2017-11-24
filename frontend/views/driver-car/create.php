<?php

use yii\helpers\Html;

$this->title = 'Создание нового автомобиля';
$this->params['breadcrumbs'][] = ['label' => 'Реестр автомобилей', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Создание';
?>
<div class="driver-car-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
