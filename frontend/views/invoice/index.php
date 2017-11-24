<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \yii\widgets\Pjax;
use \yii\bootstrap\ActiveForm;
use \kartik\date\DatePicker;
use \frontend\assets\Invoice\InvoiceIndexAsset;

InvoiceIndexAsset::register($this);

$this->title = 'Реестр счетов';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="invoice-index">
    <?php Pjax::begin() ?>

    <?php $form = ActiveForm::begin([
        'method' => 'POST',
        'successCssClass' => false,
        'options' => ['class' => 'form-horizontal', 'data-pjax' => true],
    ]); ?>
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-1">
                    <label>№</label>
                </div>

                <div class="col-md-1">
                    <label>№ заказа</label>
                </div>

                <div class="col-md-2">
                    <label>Контрагент</label>
                </div>

                <div class="col-md-1">
                    <label>Сумма</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-1">
                    <?= Html::activeTextInput($model, 'id', ['class' => 'form-control']) ?>
                </div>

                <div class="col-md-1">
                    <?= Html::activeTextInput($model, 'order_id', ['class' => 'form-control']) ?>
                </div>

                <div class="col-md-2">
                    <?= Html::activeTextInput($model, 'contractor_name', ['class' => 'form-control']) ?>
                </div>

                <div class="col-md-1">
                    <?= Html::activeTextInput($model, 'total', ['class' => 'form-control']) ?>
                </div>

                <div class="col-md-1">
                    <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary', 'style' => 'width: 100%']) ?>
                </div>

                <div class="col-md-1">
                    <button id="reset_form" class="btn btn-danger">Сбросить</button>
                </div>
            </div>

        </div>
    </div>

    <div class="box box-default">
        <div class="box-body">
            <div class="row">
                <div class="col-md-2">
                    От кого
                </div>
                <div class="col-md-1">
                    Тема
                </div>
                <div class="col-md-1">
                    Статус
                </div>
                <div class="col-md-1">
                    Основной
                </div>
                <div class="col-md-1">
                    Менеджер
                </div>
                <div class="col-md-2">
                    Дата создания
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 filter-checkboxlist">
                    <label><?= Html::checkbox('all', false, ['class' => 'check-all']) ?> Все</label>
                    <?= Html::activeCheckboxList($model, 'organization_id', $organizations, [
                        'separator' => '<br>'
                    ]) ?>
                </div>

                <div class="col-md-1 filter-checkboxlist">
                    <label><?= Html::checkbox('all', true, ['class' => 'check-all']) ?> Все</label>
                    <?= Html::activeCheckboxList($model, 'subject_id', $subjects, [
                        'separator' => '<br>'
                    ]) ?>
                </div>

                <div class="col-md-1 filter-checkboxlist">
                    <label><?= Html::checkbox('all', true, ['class' => 'check-all']) ?> Все</label>
                    <?= Html::activeCheckboxList($model, 'status_id', $statuses, [
                        'class' => 'checkboxlist-container',
                        'separator' => '<br>'
                    ]) ?>
                </div>

                <div class="col-md-1 filter-checkboxlist">
                    <label><?= Html::checkbox('all', true, ['class' => 'check-all']) ?> Все</label>
                    <?= Html::activeCheckboxList($model, 'primary', [1 => 'Да', 0 => 'Нет'], [
                        'class' => 'checkboxlist-container',
                        'separator' => '<br>'
                    ]) ?>

                </div>
                <div class="col-md-1 filter-checkboxlist">
                    <label><?= Html::checkbox('all', true, ['class' => 'check-all']) ?> Все</label>
                    <?= Html::activeCheckboxList($model, 'manager_id', $managers, [
                        'class' => 'checkboxlist-container',
                        'separator' => '<br>'
                    ]) ?>
                </div>
                <div class="col-md-2">
                    <?= DatePicker::widget([
                        'model' => $model,
                        'attribute' => 'created_from',
                        'language' => 'ru',
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'pickerButton' => false,
                        'options' => ['placeholder' => 'с'],
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy',
                            'autoclose'=>true,
                            'todayHighlight' => true
                        ]
                    ]) ?>

                    <?= DatePicker::widget([
                        'model' => $model,
                        'attribute' => 'created_to',
                        'language' => 'ru',
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'pickerButton' => false,
                        'options' => ['placeholder' => 'по'],
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy',
                            'autoclose'=>true,
                            'todayHighlight' => true
                        ]
                    ]) ?>

                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="box box-default">
        <div class="box-header with-border">
            <?php if (Yii::$app->user->identity->checkRule('invoices', 2)): ?>
                <a href="create" class="btn btn-primary pull-right" data-pjax="0">Добавить</a>
            <?php endif; ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '<div class="box-body no-padding table-responsive">{items}</div><div class="box-footer">{pager}</div>',
            'columns' => [
                'id' => [
                    'headerOptions' => ['style' => 'width: 4%;'],
                    'label' => '№',
                    'format' => 'raw',
                    'attribute' => 'id',
                    'value' => function($data) {
                        return Html::a($data->id, ['update', 'id' => $data->id], [
                            'data-pjax' => '0',
                        ]);
                    },
                ],
                'order_id' => [
                    'headerOptions' => ['style' => 'width: 4%;'],
                    'label' => 'Заказ',
                    'attribute' => 'order_id',
                    'value' => function($data) {
                        return $data->order_id;
                    },
                ],
                'organization_id' => [
                    'label' => 'От кого',
                    'attribute' => 'organization_id',
                    'value' => function($data) {
                        return $data->organization->organization_name;
                    }
                ],
                'contractor' => [
                    'attribute' => 'contractor_name',
                    'label' => 'Контрагент',
                    'value' => function($data) {
                        return $data->contractor->contractor_name . ', ' . $data->contact_person->contact_person_name;
                    },
                ],
                'subject_id' => [
                    'attribute' => 'subject_id',
                    'label' => 'Тема',
                    'value' => function($data) {
                        return $data->subject->name;
                    },
                ],
                'total' => [
                    'headerOptions' => ['style' => 'width: 6%;'],
                    'attribute' => 'total',
                    'label' => 'Сумма',
                    'value' => function($data) {
                        return str_replace('.', ',', floatval($data->total));
                    },
                ],
                'status_id'  => [
                    'attribute' => 'status_id',
                    'label' => 'Статус',
                    'value' => function($data) {
                        return $data->status->name;
                    },
                ],
                'primary' => [
                    'attribute' => 'primary',
                    'label' => 'Основной',
                    'value' => function($data) {
                        return $data->primary ? 'Да' : 'Нет';
                    },
                ],
                'manager_id' => [
                    'attribute' => 'manager_id',
                    'label' => 'Менеджер',
                    'value' => function($data) {
                        return $data->manager->getShortName();
                    },
                ],
                'created' => [
                    'headerOptions' => ['style' => 'width:12%;'],
                    'attribute' => 'created',
                    'label' => 'Дата создания',
                    'value' => function($data) {
                        return (new \DateTime($data->created))->format('d.m.y H:i');
                    },
                ],
            ],
        ]); ?>
    <?php Pjax::end() ?>
</div>
