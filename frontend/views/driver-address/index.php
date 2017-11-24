<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Адреса доставок';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="driver-address-index">

    <div class="box box-default">
        <div class="box-header with-border">
                <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-primary pull-right']) ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout'=>'<div class="box-body no-padding table-responsive">{items}</div><div class="box-footer">{pager}</div>',
            'columns' => [
                'id'=> [
                    'label' => '№',
                    'format' => 'html',
                    'value' => function($data) {
                        return Html::a($data->id, ['view', 'id' => $data->id]);
                    },
                ],
                'name',
                'address',
                'region',
                'from' => [
                    'label' => 'Пункт загрузки',
                    'value' => function($data) {
                        if ($data->from) return 'Да';
                            else return 'Нет';
                    }
                ],
                'to' => [
                    'label' => 'Пункт разгрузки',
                    'value' => function($data) {
                        if ($data->to) return 'Да';
                            else return 'Нет';
                    }
                ],
                'tk' => [
                    'label' => 'Транспортная компания',
                    'value' => function($data) {
                        if ($data->tk) return 'Да';
                            else return 'Нет';
                    }
                ],
            ],
        ]); ?>
    </div>
</div>
