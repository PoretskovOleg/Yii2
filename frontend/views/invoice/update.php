<?php

$this->title = 'Редактировать счёт';
$this->params['breadcrumbs'][] = ['label' => 'Реестр счетов', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id];
$this->params['breadcrumbs'][] = 'Редактировать';

?>

<div class="invoice-update">
    <?= $this->render('_form', $_params_) ?>
</div>
