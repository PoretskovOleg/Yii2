<?php

$this->title = 'Добавить коммерческое предложение';
$this->params['breadcrumbs'][] = ['label' => 'Реестр коммерческих предложений', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="commercial-proposal-create">

    <?= $this->render('_form', $_params_) ?>
</div>
