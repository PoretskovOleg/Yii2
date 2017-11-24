<?php

use yii\helpers\Html;

$this->title = 'Создание нового адреса';
$this->params['breadcrumbs'][] = ['label' => 'Адреса доставок', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Создание';
?>
<div class="driver-address-create">

    <?= $this->render('_form', [
        'model' => $model,
        'region' => $region
    ]) ?>

</div>
