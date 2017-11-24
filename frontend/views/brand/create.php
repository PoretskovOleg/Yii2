<?php

use yii\helpers\Html;

$this->title = 'Добавить бренд';
$this->params['breadcrumbs'][] = ['label' => 'Наши бренды', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="brand-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
