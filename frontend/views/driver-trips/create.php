<?php

use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => 'Реестр поездок', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Создание поездки';
?>
<div class="driver-trips-create">

    <?= $this->render('_form', [
        'model' => $model,
        'edit' => $edit,
        'modelComment' => $modelComment,
        'isAddressFrom' =>$isAddressFrom,
        'isAddressTo' => $isAddressTo
    ]) ?>

</div>
