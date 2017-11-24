<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Реестр автомобилей', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Просмотр';
?>
<div class="driver-car-view">
    <div class="box box-default">
        <div class="box-header with-border pull-right"> 

                <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить этот автомобиль?',
                        'method' => 'post',
                    ],
                ]) ?>
        </div>
        <div class="box-body no-padding">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'name',
                    'number',
                ],
            ]) ?>
        </div>
    </div>
</div>
