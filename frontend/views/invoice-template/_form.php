<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\widgets\MultiSelect;
use frontend\widgets\HtmlEditor;
use frontend\assets\Invoice\InvoiceTemplateFormAsset;
use yii\widgets\Pjax;

InvoiceTemplateFormAsset::register($this);

$fieldOptions = [
    'template' => '
        <div class="col-md-9 col-md-offset-2">{error}</div>
        {label}
        <div class="col-md-9">{input}{hint}</div>
    ',
    'labelOptions' => ['class' => 'control-label col-md-2'],
];

$this->registerJs('window.open_pdf = false;');

?>

<div class="template-form">

    <div class="box box-default">

        <?php Pjax::begin(); ?>
        <?php $form = ActiveForm::begin(['options' => ['class' => 'form-horizontal', 'data-pjax' => true], 'successCssClass' => false]); ?>
        <div class="box-body">

            <?= $form->field($model, 'name', $fieldOptions)->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'subject', $fieldOptions)->dropDownList($subjects, ['prompt' => 'Выберите тему']) ?>

            <?= $form->field($model, 'amount_greater_than', $fieldOptions)->textInput() ?>

            <?= $form->field($model, 'amount_less_than', $fieldOptions)->textInput() ?>

            <?=
                $form->field($model, 'needs_approval_by_ids', $fieldOptions)->widget(
                    MultiSelect::className(),
                    [
                        'data' => $positions,
                        'options' => [
                            'placeholder' => 'Выберите утверждающих',
                            'multiple' => true,
                        ],
                    ]
                );
            ?>

            <?= $form->field($model, 'organization', $fieldOptions)->dropDownList(
                    $organizations,
                    ['prompt' => 'Выберите юридическое лицо']
                ) ?>

            <?php $disabled = false; ?>
            <?php if (empty($signers)) $disabled = true; ?>

            <?= $form->field($model, 'signer', $fieldOptions)->dropDownList($signers, ['disabled' => $disabled]) ?>

            <?= $form->field($model, 'prepayment_percentage', $fieldOptions)->textInput() ?>

            <?= $form->field($model, 'term_days', $fieldOptions)->textInput() ?>

            <?= $form->field($model, 'delivery_stock', $fieldOptions)->dropDownList($stocks) ?>

            <?= $form->field($model, 'note', $fieldOptions)->widget(HtmlEditor::className()) ?>

            <?=
                $form->field($model, 'attachments_pages_ids', $fieldOptions)->widget(
                    MultiSelect::className(),
                    [
                        'maintainOrder' => true,
                        'data' => $attachments,
                        'options' => [
                            'placeholder' => 'Выберите дополнительные листы',
                            'multiple' => true,
                        ],
                    ]
                )
                ->hint('Выберите листы в порядке прикрепления');
            ?>

            <div class="form-promo-file">
                <?php if (isset($model->promos_filenames['2'])): ?>
                        <div class="row form-group">
                            <label class="control-label col-md-2">Рекламная картинка 2 см</label>
                            <div class="col-md-4">
                                <?= Html::img(
                                    Yii::getAlias('@invoices/promos/') . $model->promos_filenames['2'],
                                    ['class' => 'img-responsive img-thumbnail']
                                ) ?>
                            </div>
                        </div>
                        <?= $form->field($model, 'promo_2_file', $fieldOptions)
                            ->fileInput()
                            ->label('Изменить')
                            ->hint('Лимит 2 Мб, только jpg и png')
                        ?>

                <?php else: ?>
                    <?= $form->field($model, 'promo_2_file', $fieldOptions)
                        ->fileInput()
                        ->label('Рекламная картинка 2 см')
                        ->hint('Лимит 2 Мб, только jpg и png')
                    ?>
                <?php endif; ?>
            </div>

            <div class="form-promo-file">
                <?php if (isset($model->promos_filenames['5'])): ?>
                    <div class="row form-group">
                        <label class="control-label col-md-2">Рекламная картинка 5 см</label>
                        <div class="col-md-4">
                            <?= Html::img(
                                Yii::getAlias('@invoices/promos/') . $model->promos_filenames['5'],
                                ['class' => 'img-responsive img-thumbnail']
                            ) ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'promo_5_file', $fieldOptions)
                        ->fileInput()
                        ->label('Изменить')
                        ->hint('Лимит 2 Мб, только jpg и png')
                    ?>
                <?php else: ?>
                    <?= $form->field($model, 'promo_5_file', $fieldOptions)
                        ->fileInput()
                        ->label('Рекламная картинка 5 см')
                        ->hint('Лимит 2 Мб, только jpg и png')
                    ?>
                <?php endif; ?>
            </div>

            <div class="form-promo-file">
                <?php if (isset($model->promos_filenames['8'])): ?>
                    <div class="row form-group">
                        <label class="control-label col-md-2">Рекламная картинка 8 см</label>
                        <div class="col-md-4">
                            <?= Html::img(
                                Yii::getAlias('@invoices/promos/') . $model->promos_filenames['8'],
                                ['class' => 'img-responsive img-thumbnail']
                            ) ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'promo_8_file', $fieldOptions)
                        ->fileInput()
                        ->label('Изменить')
                        ->hint('Лимит 2 Мб, только jpg и png')
                    ?>
                <?php else: ?>
                    <?= $form->field($model, 'promo_8_file', $fieldOptions)
                        ->fileInput()
                        ->label('Рекламная картинка 8 см')
                        ->hint('Лимит 2 Мб, только jpg и png')
                    ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="box-footer">
            <div class="pull-right">
                <?php if (!$model->isNewRecord): ?>
                    <?= Html::button('Образец PDF', [
                        'id' => 'get_pdf_button',
                        'target' => '_blank',
                        'class' => 'btn btn-default'
                    ]) ?>
                <?php endif; ?>
                <?= Html::submitButton('Сохранить',
                    ['id' => 'invoice-template_submit', 'class' => 'btn btn-primary'])
                ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

        <?php
            $pdf_url = \yii\helpers\Url::toRoute('invoice-template/get-pdf?id=' . $model->id);
            $this->registerJs('window.pdf_url = ' . json_encode($pdf_url) . ';');
        ?>

        <?php Pjax::end(); ?>
    </div>
</div>

