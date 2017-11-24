<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Адреса доставок', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Просмотр';
?>
<div class="driver-address-view">
    <div class="box box-default">
        <div class="box-header with-border pull-right"> 
                <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите удалить данный адрес?',
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
                    'address',
                    'region',
                    'from' => [
                        'label' => 'Пункт загрузки',
                        'value' => function($data)
                        {
                            if ($data->from) return 'Да';
                            else return 'Нет';
                        }
                    ],
                    'to' => [
                        'label' => 'Пункт разгрузки',
                        'value' => function($data)
                        {
                            if ($data->to) return 'Да';
                            else return 'Нет';
                        }
                    ],
                    'tk' => [
                        'label' => 'Транспортная компания',
                        'value' => function($data)
                        {
                            if ($data->tk) return 'Да';
                            else return 'Нет';
                        }
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
