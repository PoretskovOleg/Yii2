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

<div class="driver-trips-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'post',
        'successCssClass' => false
    ]); ?>
    <div class="row box-header with-border">
        <div class='col-md-2'>
            <?= $form->field($model, 'tripId', ['template' => '
                <div class="col-md-9 col-md-offset-2">{error}</div>
                {label}
                <div class="col-md-4 no-padding">{input}{hint}</div>
            ',
            'labelOptions' => ['class' => 'control-label col-md-7 no-padding'],
            ])->textInput(); ?>
        </div>
        <div class='col-md-2'>
            <?= $form->field($model, 'tripTicketId', ['template' => '
                <div class="col-md-9 col-md-offset-2">{error}</div>
                {label}
                <div class="col-md-4 no-padding">{input}{hint}</div>
            ',
            'labelOptions' => ['class' => 'control-label col-md-8 no-padding'],
            ])->textInput(); ?>
        </div>
        <div class='col-md-3'>
            <?= $form->field($model, 'orderNumber', ['template' => '
                <div class="col-md-9 col-md-offset-2">{error}</div>
                {label}
                <div class="col-md-2 no-padding">{input}{hint}</div>
            ',
            'labelOptions' => ['class' => 'control-label col-md-8 no-padding'],
            ])->textInput(); ?>
        </div>
         <div class="col-md-3">
            <?= $form->field($model, 'organization', ['template' => '
                <div class="col-md-9 col-md-offset-2">{error}</div>
                {label}
                <div class="col-md-8 no-padding" style="width: 240px;">{input}{hint}</div>
            ',
            'labelOptions' => ['class' => 'control-label col-md-3 no-padding', 'style' => 'margin-top: 7px; margin-right: 5px; width: 90px;'],
            'inputOptions' => ['style' => 'width: 230px;']
            ])->textInput(); ?>
        </div>
        <div class="col-md-1">
            <?= Html::submitButton('Поиск', ['class' => 'btn btn-success btn-block'])?>
        </div>
        <div class="col-md-1">
            <?= Html::submitButton('Сброс', ['class' => 'btn btn-danger btn-block', 'name' => 'reset', 'value' => 'reset'])?>
        </div>
    </div>

    <div class="choice box-body no-padding">

        <div class="col-sm-1">
            <?= $form->field($model, 'typeOfTrip')
                ->checkboxList($typeOfTrip, [
                    'id' => 'typeTrip',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-sm-1">
            <?= $form->field($model, 'from')
                ->checkboxList($from, [
                    'id' => 'from',
                    'separator' => '<br>'
                ]); ?>
        </div>
        
        <div class="col-sm-1">
            <?= $form->field($model, 'to')
                ->checkboxList($to, [
                    'id' => 'to',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-sm-1">
            <?= $form->field($model, 'priority')
                ->checkboxList($priority, [
                    'id' => 'priority',
                    'separator' => '<br>'
                ]); ?>
        </div>

        <div class="col-sm-1">
            <?= $form->field($model, 'region')
                ->checkboxList($region, [
                    'id' => 'region',
                    'separator' => '<br>'
                ]); ?>
        </div>

        <div class="col-sm-1">
            <?= $form->field($model, 'authorId')
                ->checkboxList($author, [
                    'id' => 'author',
                    'separator' => '<br>'
                ]);?>
        </div>

        <div class="col-sm-1">
            <?= $form->field($model, 'status')
                ->checkboxList($status, [
                    'id' => 'status',
                    'separator' => '<br>'
                ]); ?>
        </div>

        <div class="col-sm-1">
            <?= $form->field($model, 'driverId')
                ->checkboxList($driver, [
                    'id' => 'driver',
                    'separator' => '<br>'
                ]); ?>
        </div>

        <div class="col-sm-1">
            <?= $form->field($model, 'carId')
                ->checkboxList($car, [
                    'id' => 'car',
                    'separator' => '<br>'
                ]); ?>
        </div>

        <div class="col-sm-1">
            <?= $form->field($model, 'dateCreateFrom')
                    ->widget(DatePicker::className(), $datePickerOptionFrom); ?>

            <?= $form->field($model, 'dateCreateTo')->label(false)
                    ->widget(DatePicker::className(), $datePickerOptionTo); ?>

            <?= $form->field($model, 'desiredDateBegin')
                    ->widget(DatePicker::className(), $datePickerOptionFrom); ?>

            <?= $form->field($model, 'desiredDateEnd')->label(false)
                    ->widget(DatePicker::className(), $datePickerOptionTo); ?>
        </div>

        <div class="col-sm-1">
            <?= $form->field($model, 'dateFirstFrom')
                    ->widget(DatePicker::className(), $datePickerOptionFrom); ?>

            <?= $form->field($model, 'dateFirstTo')->label(false)
                    ->widget(DatePicker::className(), $datePickerOptionTo); ?>

            <?= $form->field($model, 'dateTripFrom')
                    ->widget(DatePicker::className(), $datePickerOptionFrom); ?>

                    <?= $form->field($model, 'dateTripTo')->label(false)
                    ->widget(DatePicker::className(), $datePickerOptionTo); ?> 
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
