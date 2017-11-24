<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

$datePickerOptionFrom = [
    'language' => 'ru',
    'type' => DatePicker::TYPE_COMPONENT_APPEND,
    'pickerButton' => false,
    'options' => ['placeholder' => 'с'],
    'pluginOptions' => [
        'format' => 'dd.mm.yyyy',
        'autoclose'=>true,
        'todayHighlight' => true
]];
$datePickerOptionTo = [
    'language' => 'ru',
    'type' => DatePicker::TYPE_COMPONENT_APPEND,
    'pickerButton' => false,
    'options' => ['placeholder' => 'по'],
    'pluginOptions' => [
        'format' => 'dd.mm.yyyy',
        'autoclose'=>true,
        'todayHighlight' => true
]];

?>

<div class="production-order-search">

    <?php $form = ActiveForm::begin(['successCssClass' => false]); ?>

    <div class="box-header with-border no-padding">
        <div class="col-md-1 form-horizontal number-product-order">
            <?= $form->field($model, 'orderProductNumber', ['template' => '
                <div class="col-md-12">{error}</div>
                {label}
                <div class="col-md-6 no-padding">{input}{hint}</div>
            ',
            'labelOptions' => ['class' => 'col-md-5 control-label']
            ]); ?>
        </div>

        <div class="col-md-1 form-horizontal number-order">
            <?= $form->field($model, 'orderNumber', ['template' => '
                <div class="col-md-12">{error}</div>
                {label}
                <div class="col-md-6 no-padding">{input}{hint}</div>
            ',
            'labelOptions' => ['class' => 'col-md-6 control-label']
            ]); ?>
        </div>

        <div class="col-md-2 form-horizontal name-good">
            <?= $form->field($model, 'nameGood', ['template' => '
                <div class="col-md-12">{error}</div>
                {label}
                <div class="col-md-8 no-padding">{input}{hint}</div>
            ',
            'labelOptions' => ['class' => 'col-md-4 control-label']
            ]); ?>
        </div>

        <div class="col-md-2 buttons">
            <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton('Сброс', ['name' => 'reset', 'value' => 'reset', 'class' => 'btn btn-danger', 'style' => 'margin-left: 10px']) ?>
        </div>

    </div>

    <div class="box-body no-padding selection">
        <div class="col-md-1">
            <?= $form->field($model, 'priorities')
                ->checkboxList($priority, [
                    'id' => 'priority',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'targets')
                ->checkboxList($target, [
                    'id' => 'target',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'themes')
                ->checkboxList($theme, [
                    'id' => 'theme',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <div>
                <?= $form->field($model, 'typesGood')
                    ->checkboxList($typeGood, [
                        'id' => 'typeGood',
                        'separator' => '<br>'
                    ]); ?>
            </div>
        
            <div>
                <?= $form->field($model, 'typesOrder')
                    ->checkboxList($typeOrder, [
                        'id' => 'typeOrder',
                        'separator' => '<br>'
                    ]); ?>
            </div>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'responsibles')
                ->checkboxList($responsible, [
                    'id' => 'responsible',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'otks')
                ->checkboxList($otk, [
                    'id' => 'otk',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'statuses')
                ->checkboxList($status, [
                    'id' => 'status',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'stages')
                ->checkboxList($stage, [
                    'id' => 'stage',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-sm-1">
            <?= $form->field($model, 'dateCreateFrom')
                    ->widget(DatePicker::className(), $datePickerOptionFrom); ?>

            <?= $form->field($model, 'dateCreateTo')->label(false)
                    ->widget(DatePicker::className(), $datePickerOptionTo); ?>

            <?= $form->field($model, 'dateDedlineFrom')
                    ->widget(DatePicker::className(), $datePickerOptionFrom); ?>

            <?= $form->field($model, 'dateDedlineTo')->label(false)
                    ->widget(DatePicker::className(), $datePickerOptionTo); ?>
        </div>
        <div class="col-sm-1">
            <?=$form->field($model, 'typeSort')
                    ->radioList([0 => 'Стандарт', 1 => 'Похожие'], ['id' => 'typeSort', 'separator' => '<br>', 'value' => empty($model->typeSort) ? 0 : $model->typeSort]); 
                ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
