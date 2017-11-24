<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use common\models\Production\ProductionTheme;
use common\models\Production\ProductionPriority;
use common\models\Production\ProductionTarget;
use common\models\Production\ProductionStageOrder;
use common\models\Old\OrderGood;
use common\models\Old\Order;

$fieldOptions = [
    'template' => '
        <div class="col-sm-9 col-sm-offset-2">{error}</div>
        {label}
        <div class="col-sm-9">{input}{hint}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2'],
    'hintOptions' => ['class' => 'help-block']
];

?>

<div class="production-order-form">

    <?php $form = ActiveForm::begin([
        'layout'=>'horizontal',
        'successCssClass' => false,
    ]); ?>

    <div class="box box-default">
        <div class="box-body">
            <?= $form->field($model, 'theme', $fieldOptions)->dropdownList(
                ProductionTheme::find()->select(['name', 'id'])->indexBy('id')->column(),
                ['prompt' => '']
            ); ?>

            <?php if (Yii::$app->user->identity->checkRule('production-order', 3)) : ?>
                <?= $form->field($model, 'priority', $fieldOptions)->dropdownList(
                    ProductionPriority::find()->select(['name', 'id'])->indexBy('id')->column(),
                    ['options' => $model->priority ? array() : ['3' => ['selected' => true]]]
                )->hint('По умолчанию "обычный". Если требуется повысить приоритет, отправляйте письмо на pdo1@azavod.ru'); ?>
            <?php else : ?>
                <?= $form->field($model, 'priority', $fieldOptions)->dropdownList(
                    ProductionPriority::find()->select(['name', 'id'])->indexBy('id')->column(),
                    ['disabled' => true, 'options' => $model->priority ? array() : ['3' => ['selected' => true]]]
                )->hint('По умолчанию "обычный". Если требуется повысить приоритет, отправляйте письмо на pdo1@azavod.ru'); ?>
                <div hidden>
                    <?= $form->field($model, 'priority', $fieldOptions)->dropdownList(
                    ProductionPriority::find()->select(['name', 'id'])->indexBy('id')->column(),
                    ['options' => $model->priority ? array() : ['3' => ['selected' => true]]]); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Примечание:</h3>
        </div>
        <div class="box-body">
            <?= $form->field($model, 'notice', $fieldOptions)->textarea()
                ->hint('Напишите важные примечания для производства: отклонения от стандартной продукции, цвет, размеры, другие нюансы'); ?>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Что:</h3>
        </div>
        <div class="box-body">
            <?php if ($model->isNewRecord) : ?>
                <?=$form->field($model, 'target', $fieldOptions)
                    ->inline()->radioList(ProductionTarget::find()->select(['name','id'])
                        ->indexBy('id')->column(), ['value' => 1]); 
                ?>
                <div id="order">
                    <?= $form->field($model, 'order', ['template' =>
                        '<div class="col-sm-9 col-sm-offset-2">{error}</div>
                        {label}
                        <div class="col-sm-9 input-group" style="padding: 0 15px;">
                            {input}
                            <span class="input-group-btn search-goods">
                                <button type="button" class="btn btn-success btn-flat">
                                    <span class="glyphicon glyphicon-search"></span>
                                </button>
                            </span>
                        </div><div class="col-sm-9 col-sm-offset-2">{hint}</div>',
                    'inputOptions' => ['class' => 'form-control', 'placeholder' => '№ счета, например, T-8815', 'id' => 'orderNumber'],
                    'labelOptions' => ['class' => 'control-label col-sm-2'],
                    'hintOptions' => ['class' => 'help-block']
                    ])->textInput()->hint('Укажите номер заказа к которому создается заказ-наряд'); ?>
                </div>

                <div class="col-sm-9 col-sm-offset-2 no-padding" id="catalog" hidden>
                    <button type="button" class="btn btn-info" data-toggle='modal' data-target='#add_material'>Выбрать из каталога</button>
                </div>

                <div id="another" hidden>
                    <?= $form->field($model, 'nameGood', $fieldOptions)->textInput(); ?>

                    <?= $form->field($model, 'countStock', [
                        'template' => '
                            <div class="col-sm-9 col-sm-offset-2">{error}</div>
                            {label}
                            <div class="col-sm-9">{input}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-2'],
                        'hintOptions' => ['class' => 'help-block'],
                        'inputOptions' => ['type' => 'number', 'min' => 1, 'step' => 1]
                    ])->textInput(); ?>
                </div>

                <div id="listGoods" class="col-sm-12 no-padding" hidden>
                    <div class="col-sm-2" style="text-align: right;"> <b style="padding-right: 10px;">Список товаров</b> </div>
                    <div class="col-sm-9 no-padding">
                        <table class="table table-striped table-bordered data-table table-condensed">
                            <thead>
                                <tr>
                                    <th width="7%">id</th>
                                    <th width="59%">Наименование</th>
                                    <th width="8%">Кол-во</th>
                                    <th width="8%">Ед.изм.</th>
                                    <th width="15%">Тип<span class="red-text">*</span></th>
                                    <th width="3%"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="listStockGoods" class="col-sm-12 no-padding" hidden>
                    <div class="col-sm-2" style="text-align: right;"> <b style="padding-right: 10px;">Список товаров</b> </div>
                    <div class="col-sm-9 no-padding">
                        <table class="table table-striped table-bordered data-table table-condensed">
                            <thead>
                                <tr>
                                    <th width="7%">id</th>
                                    <th width="57%">Наименование</th>
                                    <th width="8%">Ед.изм.</th>
                                    <th width="10%">Кол-во</th>
                                    <th width="15%">Тип<span class="red-text">*</span></th>
                                    <th width="3%"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-sm-12 no-padding" style="margin-bottom: 15px;">
                    <div class="col-sm-2" style="text-align: right;"> <b style="padding-right: 10px;">Назначение</b></div>
                    <div class="col-sm-9 no-padding">
                        <?=$model->targetOrder->name; ?>
                    </div>
                </div>
                <div class="col-sm-12 no-padding" style="margin-bottom: 15px;">
                    <div class="col-sm-2" style="text-align: right;"> <b style="padding-right: 10px;">Наименование</b></div>
                    <div class="col-sm-9 no-padding">
                        <?=(empty($model->good) ? 
                            $model->nameGood : (!empty($model->goodOrder) ? 
                                (!empty($model->goodOrder->goods_name) ? 
                                    $model->goodOrder->goods_name : $model->goodOrder->name) : '')); ?>
                    </div>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, ($model->target == 1 ? 'countOrder' : 'countStock'), [
                        'template' => '
                            <div class="col-sm-12">{error}</div>
                            {label}
                            <div class="col-sm-6 no-padding">{input}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-6', 'style' => 'text-align: left;'],
                        'hintOptions' => ['class' => 'help-block'],
                        'inputOptions' => ['type' => 'number', 'step' => 1, 'min' => 1,
                            'max' => empty($maxQuantity) ? '' : $maxQuantity]
                    ])->textInput(); ?>
                </div>
                <div class="col-sm-4">
                    <?= $form->field($model, 'typeGood', [
                        'template' => '
                            <div class="col-sm-12">{error}</div>
                            {label}
                            <div class="col-sm-7">{input}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-5'],
                        'hintOptions' => ['class' => 'help-block']
                    ])->dropdownList([1 => 'Типовое', 2 => 'Индивидуальное']); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($selectStages)) : ?>
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Производство:</h3>
            </div>
            <div class="box-body">
                <div style="margin-bottom: 30px;">
                    <div class="col-sm-2" style="text-align: right;"> <b style="padding-right: 10px;">Ответственный</b></div>
                    <div class="col-sm-9 no-padding">
                        <?=(empty($model->responsible) ? '-' : $model->responsibleOrder->shortName); ?>
                    </div>
                </div>

                <div>
                    <div class="col-sm-2" style="text-align: right;"> <b style="padding-right: 10px;">Этапы</b></div>
                    <div class="col-sm-9 no-padding">
                        <?php foreach ($selectStages as $stage) :?>
                            <span> <?=$stage->nameStage->name;?>; &nbsp; </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($stagesFiles)) : ?>
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Файлы этапов:</h3>
            </div>
            <div class="box-body no-padding">
                <?php foreach ($stagesFiles as $stage => $files) : ?>
                    <div class="col-sm-12 no-padding" style="margin: 10px 0;">
                        <div class="col-sm-2" style="text-align: right;"> <b><?=ProductionStageOrder::findOne($stage)->name; ?></b> </div>
                        <div class="col-sm-9">
                            <?php foreach ($files as $file) : ?>
                                <span><a href="/files/production-order-files/<?=$file->name; ?>" target="_blank"><?=$file->name; ?></a>
                                <?php if ($file->statusStage->status == 2) : ?>
                                    <span class="glyphicon glyphicon-trash" id="file_<?=$file->id?>"></span>
                                <?php endif; ?>
                            </span><span> ; </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($model->status == 4 && (Yii::$app->user->identity->user_id == $model->responsible || Yii::$app->user->identity->checkRule('production-order', 8))) : ?>
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Загрузка файлов:</h3>
            </div>
            <div class="box-body">
                <?= $form->field($model, 'orderFiles[]', $fieldOptions)->fileInput(['multiple' => true])
                    ->label('Этап "' . $model->stageOrder->name . '"')
                    ->hint('Прикрепите файл для этапа '. $model->stageOrder->name); ?>
                <?php if($model->stage == 7) : ?>
                    <?= $form->field($model, 'otk', $fieldOptions)->dropdownList(
                        empty($usersOtk) ? array() : $usersOtk,
                        ['prompt' => '', 'required' => true]
                    ); ?>
                <?php endif; ?>
            </div>
            <div class="box-footer buttons">
                <div class="col-sm-2 pull-right">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'save_file', 'value' => 'save_file']) ?>
                </div>
                <div class="col-sm-2 pull-right">
                    <?= Html::submitButton('Завершить этап', ['class' => 'btn btn-success', 'name' => 'finish_stage', 'value' => 'finish_stage']) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div id="planning" class="box box-default box-solid" hidden>
        <div class="box-header with-border">
            <h3 class="box-title">Планирование:</h3>
        </div>

        <div class="box-body">
            <?= $form->field($model, 'responsible', $fieldOptions)->dropdownList(
                empty($responsibles) ? array() : $responsibles,
                ['prompt' => '']
            ); ?>
            <?= $form->field($model, 'isPlanning')->hiddenInput()->label(false); ?>
            <div class="col-sm-2" style="text-align: right;"> <b style="padding-right: 10px;">Этапы</b> </div>

            <div class="col-sm-9" >
                <?= Html::checkboxList('stages', (empty($selectStages) ? array_keys($stages) : array_keys($selectStages)), $stages, [
                    'id' => 'stagesOrder',
                ]); ?>
            </div>
        </div>
        <div class="box-footer buttons">
            <div class="col-sm-2 pull-right">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'planning', 'value' => 'planning']) ?>
            </div>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Когда:</h3>
        </div>
        <div class="box-body dedline">
            <?php if (Yii::$app->user->identity->checkRule('production-order', 3)) : ?>
                <?= $form->field($model, 'dedline', $fieldOptions)->widget(DatePicker::className(), [
                        'language' => 'ru',
                        'type' => DatePicker::TYPE_INPUT,
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy',
                            'autoclose'=>true,
                            'todayHighlight' => true
                    ]]); ?>
            <?php else: ?>
                <div class="dedline-enabled" hidden>
                    <?= $form->field($model, 'dedline', $fieldOptions)->widget(DatePicker::className(), [
                        'language' => 'ru',
                        'type' => DatePicker::TYPE_INPUT,
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy',
                            'autoclose'=>true,
                            'todayHighlight' => true
                    ]]); ?>
                </div>
                <div class="dedline-disabled">
                    <?= $form->field($model, 'dedline', $fieldOptions)->widget(DatePicker::className(), [
                        'language' => 'ru',
                        'type' => DatePicker::TYPE_INPUT,
                        'disabled' => true,
                        'pluginOptions' => [
                            'format' => 'dd.mm.yyyy',
                            'autoclose'=>true,
                            'todayHighlight' => true
                    ]]); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="box box-default">
        <div class="box-footer buttons">
            <div class="col-sm-2 pull-right">
                <?= Html::a('Отмена', ['index'] ,['class' => 'btn btn-danger']); ?>
            </div>
            <?php if($model->isNewRecord) : ?>
                <div class="col-sm-2 pull-right">
                    <?= Html::submitButton('Создать', ['class' => 'btn btn-primary']) ?>
                </div>
            <?php else: ?>
                <?php if (($model->status == 1 && (Yii::$app->user->identity->user_id == $model->author || Yii::$app->user->identity->checkRule('production-order', 4))) ||
                            (in_array($model->status,  [3, 4, 5]) && Yii::$app->user->identity->user_id == $model->responsible) ||
                            ($model->status < 6 && Yii::$app->user->identity->checkRule('production-order', 3))) : ?>
                    <div class="col-sm-2 pull-right">
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php endif; ?>
                <?php if ( ($model->status == 1 && Yii::$app->user->identity->user_id == $model->author) ||
                            (in_array($model->status, [1, 2, 3, 6, 7]) && Yii::$app->user->identity->checkRule('production-order', 6))) : ?>
                    <div class="col-sm-2 pull-right">
                        <?= Html::submitButton('Удалить', ['class' => 'btn bg-maroon', 'name' => 'delete', 'formaction' => './delete?id=' . $model->id]); ?>
                    </div>
                <?php endif; ?>
                <?php if ($model->status == 1 && Yii::$app->user->identity->checkRule('production-order', 4)) : ?>
                    <div class="col-sm-2 pull-right">
                        <?= Html::submitButton('Согласован', ['class' => 'btn btn-info', 'name' => 'agreed', 'value' => 'agreed']) ?>
                    </div>
                <?php endif; ?>
                <?php if (in_array($model->status, [2, 3, 5]) && Yii::$app->user->identity->checkRule('production-order', 5)) : ?>
                    <div class="col-sm-2 pull-right">
                        <?= Html::button('Планирование', ['id' => 'btn-planning', 'class' => 'btn btn-success']) ?>
                    </div>
                <?php endif; ?>
                <?php if (($model->status == 3 || $model->status == 5) && Yii::$app->user->identity->user_id == $model->responsible) : ?>
                    <div class="col-sm-2 pull-right">
                        <?= Html::submitButton('Взять в работу', ['class' => 'btn btn-info', 'name' => 'inWork', 'value' => 'inWork']) ?>
                    </div>
                <?php endif; ?>
                <?php if ($model->status == 4 && Yii::$app->user->identity->user_id == $model->responsible) : ?>
                    <div class="col-sm-2 pull-right">
                        <?= Html::submitButton('Приостановить', ['class' => 'btn btn-warning', 'name' => 'pause', 'value' => 'pause']) ?>
                    </div>
                <?php endif; ?> 
                <?php if (($model->status == 4 || $model->status == 5) && Yii::$app->user->identity->user_id == $model->responsible) : ?>
                    <div class="col-sm-2 pull-right">
                        <?= Html::submitButton('Отменить', ['class' => 'btn bg-maroon', 'name' => 'cancel', 'value' => 'cancel']) ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="modal fade" id="add_material" tabindex="false" role="dialog" aria-labelledby="add_good_modal_label" style="width: 100%;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="add_good_modal_label">Форма добавления материалов</h4>
                </div>
                <div class="modal-body">
                    <?= $this->render('/common/pjax/_good_search_form', ['goodSearchModel' => $goodSearchModel]) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>

</div>
