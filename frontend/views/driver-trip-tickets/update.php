<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Путевой лист № ' . $model->id . ' от ' . date('d.m.Y', $model->createdAt);
$this->params['breadcrumbs'][] = ['label' => 'Реестр путевых листов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование путевого листа';
?>
<div class="driver-trip-tickets-update">

    <?= $this->render('_form', [
        'model' => $model,
        'drivers' => $drivers,
        'dataProvider' => $dataProvider,
        'statusTripTickets' => $model->status,
        'car' => $car,
        'from' => $from,
        'edit' => true
    ]) ?>

    <div class="modal fade" id="modalYes" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <?php $formYes = ActiveForm::begin(); ?>
                    <div class="modal-body">
                        <h3>Точно успешна, пальцем не промазали?</h3>
                        <?= Html::submitButton('Да, все успешно!',
                            ['name' => 'yes_success', 'class' => 'btn btn-primary']);
                        ?>
                    </div>
                    <div class="modal-footer">
                        <?= Html::button('Отмена', ['class' => 'btn btn-danger', 'data-dismiss' => 'modal']); ?>
                    </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalNo" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Почему ваша поездка не успешна?</h4>
                </div>
                <?php $formComment = ActiveForm::begin(); ?>
                    <div class="modal-body">
                        <?= $formComment->field($modelComment, 'comment')->textarea(); ?>
                    </div>
                    <div class="modal-footer">
                        <?= Html::submitButton('ОК',
                            ['name' => 'no_success', 'class' => 'btn btn-primary']);
                        ?>
                        <?= Html::button('Отмена', ['class' => 'btn btn-danger', 'data-dismiss' => 'modal']); ?>
                    </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>

</div>
