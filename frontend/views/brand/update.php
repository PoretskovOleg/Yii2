<?php

$this->title = 'Изменить бренд: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Наши бренды', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="brand-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
