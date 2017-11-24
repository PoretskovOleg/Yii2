<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$fieldOptions = [
    'template' => '
        <div class="col-md-9 col-md-offset-2">{error}</div>
        {label}
        <div class="col-md-9">
            {input}{hint}
        </div>
    ',
    'labelOptions' => ['class' => 'control-label col-md-2'],
];
?>

<div class="attachment-form">
    <div class="box box-default">
        <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal'], 'successCssClass' => false]); ?>
        <div class="box-body">
            <?= $form->field($model, 'name', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?php $fileLabel = 'Файл'; ?>
            <?php if (!$model->isNewRecord && !empty($model->filename)): ?>
                <?php $fileLabel = 'Заменить файл'; ?>
                <div class="form-group">
                    <label class="control-label col-md-2">Файл:</label>
                    <div class="col-md-10">
                        <?= Html::a(
                            $model->filename,
                            Yii::getAlias('@attachments/' . $model->filename)
                        ) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?= $form->field($model, 'file', $fieldOptions)
                ->fileInput()
                ->label($fileLabel)
            ?>
        </div>

        <div class="box-footer">
            <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', [
                'class' => 'pull-right btn btn-primary',
            ]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
