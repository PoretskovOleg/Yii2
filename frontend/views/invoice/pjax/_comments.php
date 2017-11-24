<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\datetime\DateTimePicker;

?>

<div class="box box-solid">
    <?php if (isset($success)): ?>
        <div class="box-header">
            <div class="alert alert-<?= $success ? 'success' : 'danger' ?>">
                <?= $success ? 'Комментарий успешно добавлен' : 'Не удалось добавить комментарий' ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="box-body">
        <?php $form = ActiveForm::begin([
            'method' => 'POST',
            'action' => 'add-comment',
            'successCssClass' => false,
            'options' => [
                'data-pjax' => 1,
                'class' => 'form-horizontal',
            ],
        ]); ?>

        <?= $form->field($model, 'text')->textarea(['rows' => 3]) ?>
        <?= $form->field($model, 'deadline')->widget(DateTimePicker::className(), [
                'language' => 'ru',
                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                'pickerButton' => false,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy hh:ii',
                    'autoclose' => true,
                    'todayHighlight' => true,
                ],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Отправить', ['class' => 'pull-right btn btn-primary']) ?>
        </div>

        <?= Html::activeHiddenInput($model, 'invoice_id') ?>

        <?php ActiveForm::end() ?>
    </div>
</div>
