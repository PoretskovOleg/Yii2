<?php

$this->title = 'Изменить тему';
$this->params['breadcrumbs'][] = ['label' => 'Темы закупок', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
