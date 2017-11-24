<?php

$this->title = 'Добавить шаблон';
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны счетов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="invoice-template-create">
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
