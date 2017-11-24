<?php

use yii\helpers\Html;

$this->title = 'Добавить прикрепляемый файл';
$this->params['breadcrumbs'][] = ['label' => 'Прикрепляемые файлы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Добавить';
?>

<div class="attachment-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
