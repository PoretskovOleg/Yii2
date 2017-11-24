<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

$datePickerOptionsFrom = [
    'language' => 'ru',
    'type' => DatePicker::TYPE_COMPONENT_APPEND,
    'pickerButton' => false,
    'options' => ['placeholder' => 'с'],
    'pluginOptions' => [
        'format' => 'dd.mm.yyyy',
        'autoclose'=>true,
        'todayHighlight' => true
]];
$datePickerOptionsTo = [
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

<div class="driver-trip-tickets-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'post',
        'successCssClass' => false
    ]); ?>

    <div class="box-header with-border">
        <div class='col-sm-2 no-padding'>
            <?= $form->field($model, 'id', ['template' => '
                <div class="col-sm-10 col-sm-offset-2">{error}</div>
                {label}
                <div class="col-sm-6 no-padding">{input}{hint}</div>',
            'labelOptions' => ['class' => 'control-label col-sm-6 no-padding'],
            ]) ?>
        </div>
        <div class="col-sm-offset-6 col-sm-2">
            <?= Html::submitButton('Поиск', ['class' => 'btn btn-success']) ?>
        </div>
        <div class="col-sm-2">
            <?= Html::submitButton('Сброс', ['class' => 'btn btn-danger', 'name' => 'reset', 'value' => 'reset']) ?>
        </div>
    </div>

    <div class="box-body no-padding">

        <div class="col-sm-2">
            <?= $form->field($model, 'statusTripTicket')
                ->checkboxList($status, [
                    'id' => 'status',
                    'separator' => '<br>'
                ]); ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'driverTripTicket')
                ->checkboxList($driver, [
                    'id' => 'driver',
                    'separator' => '<br>'
                ]); ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'carTripTicket')
                ->checkboxList($car, [
                    'id' => 'car',
                    'separator' => '<br>'
                ]); ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'createdFrom')
                ->widget(DatePicker::className(), $datePickerOptionsFrom); ?>

            <?= $form->field($model, 'createdTo')->label(false)
                ->widget(DatePicker::className(), $datePickerOptionsTo); ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'departureFrom')
                ->widget(DatePicker::className(), $datePickerOptionsFrom); ?>

            <?= $form->field($model, 'departureTo')->label(false)
                ->widget(DatePicker::className(), $datePickerOptionsTo); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
