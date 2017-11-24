<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\datetime\DateTimePicker;
use frontend\assets\Driver\DriverAsset;

    DriverAsset::register($this);

$fieldOptions = [
    'template' => '
        <div class="col-sm-10 col-sm-offset-2">{error}</div>
        {label}
        <div class="col-sm-4">{input}{hint}</div>
    ',
    'labelOptions' => ['class' => 'control-label col-sm-2'],
    'hintOptions' => ['class' => 'help-block']
];
?>

<div class="driver-trip-tickets-form">

    <?php $form = ActiveForm::begin([
        'layout'=>'horizontal',
        'successCssClass' => false
    ]); ?>

    <div class="box box-info">
        <div class="box-body">
            <?= $form->field($model, 'departurePlace', $fieldOptions)
                ->dropdownList($from, ['prompt' => '']); ?>

            <?= $form->field($model, 'departureDate', $fieldOptions)
                ->widget(DateTimePicker::className(), [
                    'language' => 'ru',
                    'type' => DateTimePicker::TYPE_INPUT,
                    'pluginOptions' => [
                    'format' => 'dd.mm.yyyy hh:ii',
                    'autoclose'=>true,
                    'todayHighlight' => true
                ]]); ?>

            <?= $form->field($model, 'driver', $fieldOptions)
                ->dropdownList($drivers, ['prompt' => '']); ?>

            <?= $form->field($model, 'car', $fieldOptions)
                ->dropdownList($car, ['prompt' => '']); ?>

            <?= $form->field($model, 'finishPlace', $fieldOptions)
                ->dropdownList($from, ['prompt' => '']); ?>
        </div>
    </div>

    <?= $this->render('_trips', [
        'dataProvider' => $dataProvider,
        'statusTripTickets' => $statusTripTickets
    ]) ?>

    <div class="box box-info">
        <div class="box-footer">
            <?php if (!$edit) : ?>
                <div class="col-sm-1 pull-right">
                    <?= Html::a('Отмена', ['driver-trips/index'],
                    ['class' => 'btn btn-danger']); ?>
                </div>
                <div class="col-sm-1 pull-right">
                    <?= Html::submitButton('Создать', 
                    ['class' => 'btn btn-primary', 'name' => 'create', 'value'=> 'create', 'formaction' => './create']); ?>
                </div>
            <?php else : ?>
                <?php if ($model->status == 1 && Yii::$app->user->identity->checkRule('driver', 10)) : ?>
                    <div class="col-sm-1 pull-right">
                        <?= Html::submitButton('Удалить', 
                            ['class' => 'btn btn-danger', 'name' => 'delete', 'formaction' => './delete?id='.$model->id]); ?>
                    </div>
                <?php endif; ?>

                <div class="col-sm-1 pull-right">
                    <?= Html::a('Отмена', ['index'],
                    ['class' => 'btn btn-default']); ?>
                </div>

                <?php if ( in_array($model->status, [1, 2]) && Yii::$app->user->identity->checkRule('driver', 8) ) : ?>
                    <div class="col-sm-1 pull-right">
                        <?= Html::submitButton('Сохранить', 
                            ['class' => 'btn btn-success', 'name' => 'save', 'value' => 'save', 'formaction' => './update?id='.$model->id]); ?>
                    </div>
                <?php elseif($model->status == 3) : ?>
                    <div class="col-sm-1 pull-right">
                        <?= Html::submitButton('Распечатать', 
                            ['class' => 'btn btn-success', 'name' => 'save', 'formaction' => './print?id='.$model->id.'&save=yes']); ?>
                    </div>
                <?php endif; ?>

                <?php if ($model->status == 1 && Yii::$app->user->identity->checkRule('driver', 9) ) : ?>
                    <div class="col-sm-1 pull-right">
                        <?= Html::submitButton('Утвердить', 
                            ['class' => 'btn btn-primary', 'name' => 'approve', 'value' => 'approve', 'formaction' => './update?id='.$model->id]); ?>
                    </div>
                <?php elseif($model->status == 2) : ?>
                    <div class="col-sm-1 pull-right">
                        <?= Html::submitButton('Ознакомлен', 
                            ['class' => 'btn btn-warning', 'name' => 'familiar', 'value' => 'familiar', 'formaction' => './update?id='.$model->id]); ?>
                    </div>
                    <div class="col-sm-1 pull-right">
                        <?= Html::submitButton('Не утвержден', 
                            ['class' => 'btn btn-danger', 'name' => 'new', 'value' => 'new', 'formaction' => './update?id='.$model->id]); ?>
                    </div>
                <?php endif; ?>
                <?php if ( in_array($model->status, [1, 2]) ) : ?>
                    <div class="col-sm-1 pull-right">
                        <?= Html::submitButton('Печатная форма', 
                            ['class' => 'btn btn-info', 'name' => 'save', 'formaction' => './print?id='.$model->id.'&save=no']); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
