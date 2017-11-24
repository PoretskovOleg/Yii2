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

<div class="tech-dep-project-search">

    <?php $form = ActiveForm::begin(['successCssClass' => false]); ?>

    <div class="box-header with-border no-padding">
        <div class="col-md-1 form-horizontal number-project">
            <?= $form->field($model, 'id', ['template' => '
                <div class="col-md-12">{error}</div>
                {label}
                <div class="col-md-5 no-padding">{input}{hint}</div>
            ',
            'labelOptions' => ['class' => 'col-md-7 control-label']
            ]); ?>
        </div>

        <div class="col-md-1 form-horizontal number-order no-padding">
            <?= $form->field($model, 'orderNumber', ['template' => '
                <div class="col-md-12">{error}</div>
                {label}
                <div class="col-md-5 no-padding">{input}{hint}</div>
            ',
            'labelOptions' => ['class' => 'col-md-7 control-label']
            ]); ?>
        </div>

        <div class="col-md-2 form-horizontal name-product no-padding">
            <?= $form->field($model, 'nameProduct', ['template' => '
                <div class="col-md-12">{error}</div>
                {label}
                <div class="col-md-6 no-padding">{input}{hint}</div>
            ',
            'labelOptions' => ['class' => 'col-md-6 control-label']
            ]); ?>
        </div>

        <div class="col-md-2 buttons">
            <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
            <?= Html::submitButton('Сброс', ['name' => 'reset', 'value' => 'reset', 'class' => 'btn btn-danger', 'style' => 'margin-left: 10px']) ?>
        </div>

        <div class="col-md-4 no-padding" style="width: 320px; margin-top: 10px; margin-bottom: 16px;">
            <?= Html::submitButton('Я исполнитель', ['class' => 'btn btn-primary', 'name' => 'i_contractor', 'value' => 'i_contractor', 'style' => 'padding: 6px 2px']) ?>
            <?= Html::submitButton('Я ответственный', ['class' => 'btn btn-primary', 'name' => 'i_responsible', 'value' => 'i_responsible', 'style' => 'padding: 6px 2px']) ?>
            <?= Html::submitButton('Надо утвердить', ['class' => 'btn btn-primary', 'name' => 'need_approved', 'value' => 'need_approved', 'style' => 'padding: 6px 2px']) ?>
        </div>
    </div>

    <div class="box-body no-padding selection">
        <div class="col-md-1">
            <?= $form->field($model, 'types')
                ->checkboxList($type, [
                    'id' => 'typeProject',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'statuses')
                ->checkboxList($status, [
                    'id' => 'statusProject',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'stages')
                ->checkboxList($stages, [
                    'id' => 'stagesProject',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'priorities')
                ->checkboxList($priority, [
                    'id' => 'priorityProject',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'authors')
                ->checkboxList($author, [
                    'id' => 'authorProject',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'responsibles')
                ->checkboxList($responsible, [
                    'id' => 'responsibleProject',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'contractors')
                ->checkboxList($contractor, [
                    'id' => 'contractorProject',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'approveds')
                ->checkboxList($approved, [
                    'id' => 'approvedProject',
                    'separator' => '<br>'
                ]); ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'difficulties')
                ->checkboxList($difficulty, [
                    'id' => 'difficultyProject',
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
        <div class="col-md-1">
            <?= $form->field($model, 'isArchive')
                ->checkboxList($isArchive, [
                    'id' => 'isArchive',
                    'separator' => '<br>'
                ]); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
