<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\widgets\MultiSelect;
use frontend\widgets\HtmlEditor;
use frontend\assets\CommercialProposal\CommercialProposalFormAsset;
use yii\widgets\Pjax;
use kartik\file\FileInput;


CommercialProposalFormAsset::register($this);

$fieldOptions = [
    'template' => '
        <div class="col-md-9 col-md-offset-2">{error}</div>
        {label}
        <div class="col-md-9">{input}{hint}</div>
    ',
    'labelOptions' => ['class' => 'control-label col-md-2'],
    'hintOptions' => [
        'class' => 'help-block',
    ]
];

$this->registerJs('window.open_pdf = false;');
$this->registerJs('window.units = ' . json_encode($units) . ';');

if (!empty($model->id)) {
    $this->registerJs('window.model_id = ' . $model->id . ';');
}

?>

<div class="template-form">
    <?php Pjax::begin(['id' => 'commercial_proposal_edit', 'formSelector' => '#commercial_proposal_form']); ?>
    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'commercial_proposal_form',
            'class' => 'form-horizontal',
            'data-pjax' => true
        ], 'successCssClass' => false]
    ); ?>

    <?php $this->registerJs('
            window.goods_model.init(); 
            window.additional_goods_model.init(); 
            window.totals_model.init();
        '); ?>

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Основная информация</h3>
        </div>

        <div class="box-body">
            <?= $form->field($model, 'order_id', $fieldOptions)->textInput() ?>

            <div class="form-group required">
                <label class="control-label col-md-2">Контрагент</label>
                <div class="input-contractor col-md-9">
                    <input id="contractor_name" type="text" disabled class="form-control" value="<?= !empty($model->contractor) ? $model->contractor->contractor_name : '' ?>">
                    <span class="input-group-btn">
                        <button type="button" data-toggle="modal" data-target="#contractor_modal" class="btn btn-flat btn-primary">
                            Выбрать
                        </button>
                    </span>
                </div>
            </div>

            <?= Html::activeHiddenInput($model, 'contractor'); ?>

            <?= $form->field($model, 'payer', $fieldOptions)->dropDownList($payers, ['prompt' => 'Выберите плательщика']) ?>

            <?= $form->field($model, 'contact_person', $fieldOptions)->dropDownList($contact_persons, ['prompt' => 'Выберите контактное лицо']) ?>

            <?= $form->field($model, 'subject', $fieldOptions)->dropDownList($subjects, ['prompt' => 'Выберите тему']) ?>

            <?= $form->field($model, 'brand', $fieldOptions)->dropDownList($brands, ['prompt' => 'Выберите бренд']) ?>

            <div class="form-group">
                <label class="control-label col-md-2">Менеджер</label>
                <div class="col-md-9">
                    <input class="form-control" disabled value="<?= Yii::$app->user->identity->getShortName() ?>">
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($linked_proposals)): ?>
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Связанные коммерческие</h3>
        </div>
        <div class="box-body">
            <table class="table table-striped table-centered">
                <thead>
                    <th width="5%">Основной</th>
                    <th width="10%">№ комм.</th>
                    <th width="10%">Дата</th>
                    <th width="10%">Сумма</th>
                    <th width="20%">Бренд</th>
                    <th width="20%">От кого</th>
                    <th width="20%">Кому</th>
                    <th>Статус</th>
                </thead>

                <tbody>
                    <tr>
                        <td><?= Html::checkbox('primary[]', $model->primary, ['value' => $model->id]) ?></td>
                        <td>(текущее)</td>
                        <td><?= (new \DateTime($model->created))->format('d.m.y H:i') ?></td>
                        <td><?= $model->total ?></td>
                        <td><?= $model->brand->title ?></td>
                        <td><?= $model->organization->organization_name ?></td>
                        <td><?= $model->contractor->contractor_name ?></td>
                        <td><?= $model->status->name ?></td>
                    </tr>


                    <?php foreach ($linked_proposals as $proposal): ?>
                        <tr>
                            <td><?= Html::checkbox('primary[]', $proposal->primary, ['value' => $proposal->id]) ?></td>
                            <td><?= $proposal->id ?></td>
                            <td><?= (new \DateTime($proposal->created))->format('d.m.y H:i') ?></td>
                            <td><?= $proposal->total ?></td>
                            <td><?= $proposal->brand->title ?></td>
                            <td><?= $proposal->organization->organization_name ?></td>
                            <td><?= $proposal->contractor->contractor_name ?></td>
                            <td><?= $proposal->status->name ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="box box-default">
        <?= Html::activeHiddenInput($model, 'catalogGoods', ['id' => 'catalog_goods_storage']) ?>
        <div class="box-header">
            <h3 class="box-title">Товары из каталога</h3>
            <?= Html::button('Добавить', ['class' => 'btn btn-primary pull-right', 'data-toggle' => 'modal', 'data-target' => '#add_good_modal']) ?>
        </div>

        <div class="box-body no-padding table-responsive">
            <table class="table table-striped table-bordered data-table table-condensed">
                <thead>
                    <tr>
                        <th width="2%"></th>
                        <th width="2%"></th>
                        <th width="5%">Артикул</th>
                        <th width="20%">Наименование</th>
                        <th width="5%">Кол-во</th>
                        <th width="5%">Ед.</th>
                        <th width="5%">Срок, дн.</th>
                        <th width="5%">Себес, р.</th>
                        <th width="10%">МРЦ, р.</th>
                        <th width="10%">Цена баз, р.</th>
                        <th width="5%">Скидка, %</th>
                        <th width="10%">Цена</th>
                        <th width="10%">Сумма</th>
                        <th width="10%">Маржа, %</th>
                        <th width="10%">Маржа, р.</th>
                    </tr>
                </thead>

                <tbody id="goods_table">

                </tbody>
            </table>
        </div>
    </div>

    <div class="box box-default">
        <?= Html::activeHiddenInput($model, 'additionalGoods', ['id' => 'additional_goods_storage']) ?>
        <div class="box-header">
            <h3 class="box-title">Дополнительные позиции</h3>
            <?= Html::button('Добавить', ['class' => 'btn btn-primary pull-right', 'data-toggle' => 'modal', 'data-target' => '#add_additional_good_modal']) ?>
        </div>

        <div class="box-body no-padding table-responsive">
            <table class="table table-striped table-bordered data-table table-condensed">
                <thead>
                <tr>
                    <th width="2%"></th>
                    <th width="2%"></th>
                    <th width="30%">Наименование</th>
                    <th width="5%">Кол-во</th>
                    <th width="5%">Ед.</th>
                    <th width="5%">Срок, дн.</th>
                    <th width="10%">Себес, р.</th>
                    <th width="10%">Цена</th>
                    <th width="10%">Сумма</th>
                    <th width="10%">Маржа, %</th>
                    <th>Маржа, р.</th>
                </tr>
                </thead>

                <tbody id="additional_goods_table">

                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-xs-12 col-md-offset-8 col-xs-offset-0">
            <div class="box box-default">
                <div class="box-body no-padding">
                    <table class="table table-bordered">
                        <tbody id="totals_table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Данные шаблона</h3>
        </div>
        <div class="box-body">
            <?php $isTemplateEditable = $permissions['can_edit_template_data']; ?>

            <?= Html::activeHiddenInput($model, 'template') ?>
            <?= $form->field($model, 'template', $fieldOptions)->dropDownList(
                $templates,
                ['prompt' => 'Выберите шаблон', 'id' => 'fake_template', 'disabled' => !$isTemplateEditable]
            ) ?>

            <?= Html::activeHiddenInput($model, 'organization') ?>
            <?= $form->field($model, 'organization', $fieldOptions)->dropDownList(
                    $organizations,
                    ['prompt' => 'Выберите юридическое лицо', 'id' => 'fake_organization', 'disabled' => !$isTemplateEditable]
                )
            ?>

            <?= Html::activeHiddenInput($model, 'signer') ?>
            <?= $form->field($model, 'signer', $fieldOptions)->dropDownList($signers, [
                'prompt' => 'Выберите подписанта',
                'id' => 'fake_signer',
                'disabled' => !$isTemplateEditable,
            ]) ?>

            <?= $form->field($model, 'prepayment_percentage', $fieldOptions)->textInput(['readonly' => !$isTemplateEditable]) ?>

            <?= $form->field($model, 'term_days', $fieldOptions)->textInput() ?>

            <?= $form->field($model, 'payment_method', $fieldOptions)->dropDownList([1 => 'Банковский перевод', 2 => 'Наличными']) ?>

            <?= $form->field($model, 'note', $fieldOptions)->widget(HtmlEditor::className()) ?>
        </div>
    </div>

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Доставка</h3>
        </div>
        <div class="box-body">
            <?= $form->field($model, 'delivery', $fieldOptions)->dropDownList([1 => 'Доставка', 0 => 'Самовывоз'])->label('') ?>

            <div id="select_delivery">
                <?=
                    $form->field(
                        $model,
                        'delivery_payment_type',
                        $fieldOptions
                    )->dropDownList([1 => 'Отдельной строкой в договор', 2 => 'Распределить по счёту'])
                ?>
                <?=
                    $form->field(
                        $model,
                        'delivery_address',
                        array_merge($fieldOptions, ['options' => ['class' => 'form-group required']])
                    )->textInput()
                ?>
                <?=
                    $form->field(
                        $model,
                        'delivery_price',
                        array_merge($fieldOptions, ['options' => ['class' => 'form-group required']])
                    )->textInput()
                ?>
            </div>

            <div id="select_stock">
                <?= $form->field($model, 'delivery_stock', $fieldOptions)->dropDownList($stocks) ?>
            </div>
        </div>
    </div>

    <div class="box box-default">
        <div class="box-body">
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

            <?php
                $initialPreviewConfig = [];
                $initialPreview = [];
                foreach ($model->files as $file) {
                    $initialPreviewConfig[] = [
                        'caption' => $file->filename,
                        'key' => $file->id,
                        'url' => 'delete-file',
                    ];

                    $initialPreview[] = '<a target="_blank" href="'
                        . Yii::getAlias('@web')
                        . Yii::getAlias('@commercial_proposals_attachments/')
                        . $file->filename . '">' . $file->filename . '</a>';
                }
            ?>

            <?=
                $form->field($model, 'attachments_files[]', $fieldOptions)->widget(FileInput::classname(), [
                    'pluginLoading' => false,
                    'options' => [
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'showUpload' => false,
                        'showDelete' => false,
                        'hideThumbnailContent' => true,
                        'theme' => 'explorer',
                        'uploadAsync' => true,
                        'overwriteInitial' => false,
                        'initialPreviewAsData' => false,
                        'initialPreview' => $initialPreview,
                        'initialPreviewConfig' => $initialPreviewConfig,
                        'fileActionSettings' => [
                            'showZoom' => false,
                        ],
                    ],
                ])->label('Дополнительные файлы');
            ?>
        </div>
    </div>

    <div class="box box-default">
        <div class="box-body">
            <div class="pull-right">
                <?php if (!$model->isNewRecord): ?>
                    <a href="<?= Url::toRoute(['create?instance_id=' . $model->id]) ?>" data-pjax="0" class="btn btn-default">Скопировать</a>
                    <?= Html::button('Образец PDF', [
                        'id' => 'get_pdf_button',
                        'target' => '_blank',
                        'class' => 'btn btn-default'
                    ]) ?>

                    <?php if ($permissions['can_user_send']): ?>
                        <?= Html::button('Отправить', [
                            'id' => 'send_email_button',
                            'class' => 'btn btn-default',
                        ]) ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($model->status_id !== $model::STATUS_SENT || $permissions['can_edit_after_send']): ?>
                    <?= Html::submitButton('Сохранить', ['id' => 'commercialproposal_submit', 'class' => 'btn btn-primary']) ?>
                    <a href="<?= Url::toRoute(['index']) ?>" data-pjax="0" class="btn btn-danger">Отмена</a>
                <?php endif; ?>
            </div>

            <div class="pull-left">
                <?php if (!$model->isNewRecord): ?>
                    <?= Html::hiddenInput('new_status', '', ['id' => 'new_status_input']) ?>

                    <?php if ($model->status_id == $model::STATUS_NEW || $model->status_id == $model::STATUS_CORRECTION): ?>
                        <?= Html::button('На согласование', [
                            'class' => 'btn btn-primary btn-status',
                            'data-status' => $model::STATUS_APPROVAL,
                        ]) ?>
                    <?php endif; ?>

                    <?php if ($permissions['is_user_approver'] && $model->status_id == $model::STATUS_APPROVAL): ?>
                        <?= Html::button('Согласовать', [
                            'class' => 'btn btn-success btn-status',
                            'data-status' => $model::STATUS_APPROVED,
                        ]) ?>

                        <?= Html::button('На исправление', [
                            'class' => 'btn btn-danger btn-status',
                            'data-status' => $model::STATUS_CORRECTION,
                        ]) ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

        <?php
            $pdf_url = \yii\helpers\Url::toRoute('commercial-proposal/get-pdf?id=' . $model->id);
            $this->registerJs('window.pdf_url = ' . json_encode($pdf_url) . ';');
        ?>
    </div>
    <?php Pjax::end(); ?>

    <?php if (!$model->isNewRecord): ?>
        <?php Pjax::begin(['id' => 'comments_block', 'enablePushState' => false]); ?>
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Статусы и комментарии</h3>
                <?= Html::button('Добавить', ['class' => 'btn btn-primary pull-right', 'data-toggle' => 'modal', 'data-target' => '#add_comment_modal']) ?>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <th width="10%">Статус</th>
                    <th width="10%">Дата и время</th>
                    <th width="10%">Автор</th>
                    <th width="60%">Комментарий</th>
                    <th width="10%">Дедлайн</th>
                    </thead>

                    <tbody>
                    <?php foreach ($model->comments as $comment): ?>
                        <tr>
                            <td><?= $comment->status ? $comment->status->name : '' ?></td>
                            <td><?= (new \DateTime($comment->created))->format('d.m.Y H:i:s') ?></td>
                            <td><?= $comment->user->getShortName() ?></td>
                            <td><?= $comment->text ?></td>
                            <td>
                                <?php if ($comment->deadline): ?>
                                    <?= (new \DateTime($comment->deadline))->format('d.m.Y H:i:s') ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php Pjax::end() ?>
    <?php endif; ?>


    <div class="modal fade" id="add_comment_modal" tabindex="-1" role="dialog" aria-labelledby="add_comment_modal_label">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="add_comment_modal_label">Форма добавления комментария</h4>
                </div>
                <div class="modal-body">
                    <?php Pjax::begin(['id' => 'add_comment_form', 'enablePushState' => false]); ?>
                        <?= $this->render('pjax/_comments', ['model' => $commentModel]) ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="contractor_modal" tabindex="-1" role="dialog" aria-labelledby="contractor_modal_label">
        <div class="modal-dialog modal-superlg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="contractor_modal_label">Форма поиска контрагентов</h4>
                </div>
                <div class="modal-body">
                    <?= $this->render('pjax/_contractor_search_form', $_params_) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_good_modal" tabindex="false" role="dialog" aria-labelledby="add_good_modal_label">
        <div class="modal-dialog modal-superlg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="add_good_modal_label">Форма добавления товара</h4>
                </div>
                <div class="modal-body">
                    <?= $this->render('pjax/_good_search_form', ['goodSearchModel' => $goodSearchModel]) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add_additional_good_modal" tabindex="false" role="dialog" aria-labelledby="add_additional_good_label">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="add_additional_good_label">Форма добавления товара</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="add_additional_good_form">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label class="control-label">Наименование</label>
                            </div>

                            <div class="col-md-10">
                                <input class="name form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-2">
                                <label class="control-label">Кол-во</label>
                            </div>

                            <div class="col-md-10">
                                <input class="quantity form-control" type="number">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-2">
                                <label class="control-label">Единицы</label>
                            </div>

                            <div class="col-md-10">
                                <select class="unit form-control">
                                    <?php foreach ($units as $val => $unit): ?>
                                        <option value="<?= $val ?>"><?= $unit ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-2">
                                <label class="control-label">Срок, дн.</label>
                            </div>

                            <div class="col-md-10">
                                <input class="period form-control" type="number">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-2">
                                <label class="control-label">Себес, р.</label>
                            </div>

                            <div class="col-md-10">
                                <input class="price form-control" type="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-2">
                                <label class="control-label">Цена</label>
                            </div>

                            <div class="col-md-10">
                                <input class="end-price form-control" type="text">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="add_additional_good_button" type="button" class="btn btn-primary">Добавить</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="send_email_modal" tabindex="false" role="dialog" aria-labelledby="send_email_modal_label">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="add_good_modal_label">Форма отправки</h4>
                </div>
                <div class="modal-body">
                    <?= $this->render('pjax/_send_email_form', ['model' => $emailModel, 'commercial_proposal_id' => $model->id]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

