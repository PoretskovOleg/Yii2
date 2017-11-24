<?php

$this->title = 'Редактирование адреса: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Адреса доставок', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="driver-address-update">

    <?= $this->render('_form', [
        'model' => $model,
        'region' => $region
    ]) ?>

</div>
