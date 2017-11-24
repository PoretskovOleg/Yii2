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

<div class="brand-form">
    <div class="box box-default">
        <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal'], 'successCssClass' => false]); ?>
        <div class="box-body">
            <?= $form->field($model, 'title', $fieldOptions)->textInput(['maxlength' => true]); ?>

            <?= $form->field($model, 'slogan', $fieldOptions)->textarea(['rows' => 3]) ?>

            <?= $form->field($model, 'city', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'address', $fieldOptions)->textarea(['rows' => 3]) ?>

            <?= $form->field($model, 'phone', $fieldOptions)->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '+9 (999) 999-99-99',
            ]) ?>

            <?= $form->field($model, 'federal_phone', $fieldOptions)->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '8 (800) 999-99-99',
            ]) ?>

            <?= $form->field($model, 'website', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'email', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?php $logoLabel = 'Логотип'; ?>
            <?php if (!$model->isNewRecord && !empty($model->logo_filename)): ?>
                <?php $logoLabel = 'Изменить логотип'; ?>
                <div class="form-group">
                    <label class="control-label col-md-2">Логотип:</label>
                    <div class="col-md-10">
                        <?= Html::img(
                            Yii::getAlias('@brands/logos/' . $model->logo_filename),
                            ['class' => 'img-responsive img-thumbnail']
                        ) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?= $form->field($model, 'logo_file', $fieldOptions)
                ->fileInput()
                ->label($logoLabel)
                ->hint('Лимит 2 Мб, только jpg и png') ?>
        </div>

        <div class="box-footer">
                <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', [
                    'class' => 'pull-right btn btn-primary',
                ]) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
