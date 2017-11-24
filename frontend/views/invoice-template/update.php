<?php

$this->title = 'Редактировать шаблон';
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны счетов', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name];
$this->params['breadcrumbs'][] = 'Редактировать';

?>

<div class="template-update">
    <?= $this->render('_form', [
        'model' => $model,
        'subjects' => $subjects,
        'positions' => $positions,
        'organizations' => $organizations,
        'signers' => $signers,
        'stocks' => $stocks,
        'attachments' => $attachments,
    ]) ?>
</div>
