<?php

$this->title = 'Добавить тему';
$this->params['breadcrumbs'][] = ['label' => 'Темы закупок', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
