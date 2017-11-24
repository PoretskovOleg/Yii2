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

<div class="driver-address-form">
    <div class="box box-default">
        <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal'], 'successCssClass' => false]); ?>
        <div class="box-body">
            <?= $form->field($model, 'name', $fieldOptions)->textInput(['maxlength' => true]); ?>

            <?= $form->field($model, 'address', $fieldOptions)->textInput(['maxlength' => true])
                ->hint('Введите по образцу: 111020, г. Москва, ул. Главная, д. 45, стр. 5, оф. 23'); ?>

            <?= $form->field($model, 'region', $fieldOptions)->dropdownlist($region, ['prompt' => '']); ?>

            <div class="col-sm-12">
                <div class="col-sm-offset-3 col-sm-3">
                    <?= $form->field($model, 'from')->checkbox() ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'to')->checkbox() ?>
                </div>
                <div class="col-sm-3">
                    <?= $form->field($model, 'tk')->checkbox() ?>
                </div>
            </div>

        </div>
        <div class="box-footer">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => 'pull-right btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
