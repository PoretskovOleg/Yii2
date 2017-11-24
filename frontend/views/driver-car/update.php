<?php

use yii\helpers\Html;

$this->title = 'Редактирование автомобиля: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Реестр автомобилей', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="driver-car-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
