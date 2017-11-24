<?php

$this->title = 'Добавить шаблон';
$this->params['breadcrumbs'][] = ['label' => 'Шаблоны коммерческих предложений', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="commercial-proposal-template-create">
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
