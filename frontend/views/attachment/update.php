<?php

use yii\helpers\Html;

$this->title = 'Изменить файл';
$this->params['breadcrumbs'][] = ['label' => 'Прикрепляемые файлы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить';
?>

<div class="attachment-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
