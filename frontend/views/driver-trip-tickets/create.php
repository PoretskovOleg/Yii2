<?php

use yii\helpers\Html;
use frontend\assets\Driver\DriverAsset;

    DriverAsset::register($this);

$this->title = 'Создание путевого листа';
$this->params['breadcrumbs'][] = ['label' => 'Реестр путевых листов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="driver-trip-tickets-create">

    <?= $this->render('_form', [
        'model' => $model,
        'drivers' => $drivers,
        'dataProvider' => $dataProvider,
        'statusTripTickets' => 0,
        'car' => $car,
        'from' => $from,
        'edit' => false
    ]) ?>

</div>
