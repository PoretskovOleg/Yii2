<?php

$this->title = 'Добавить счёт';
$this->params['breadcrumbs'][] = ['label' => 'Реестр счетов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="invoice-create">

    <?= $this->render('_form', $_params_) ?>
</div>
