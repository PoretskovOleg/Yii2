<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$fieldOptions = [
    'template' => '
        <div class="col-md-9 col-md-offset-2">{error}</div>
        {label}
        <div class="col-md-9">{input}{hint}</div>
    ',
    'labelOptions' => ['class' => 'control-label col-md-2'],
];
?>

<div class="driver-car-form">
    <div class="box box-default">
        <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal'], 'successCssClass' => false]); ?>
        <div class="box-body">
            <?= $form->field($model, 'name', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'number', $fieldOptions)->textInput(['maxlength' => true]) ?>
        </div>
        <div class="box-footer">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => 'pull-right btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
