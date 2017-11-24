<?php

$this->title = 'Редактировать коммерческое предложение';
$this->params['breadcrumbs'][] = ['label' => 'Реестр коммерческих предложений', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id];
$this->params['breadcrumbs'][] = 'Редактировать';

?>

<div class="commercial-proposal-update">
    <?= $this->render('_form', $_params_) ?>
</div>
