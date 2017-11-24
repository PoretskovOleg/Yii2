<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Реестр автомобилей';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="driver-car-index">
    <div class="box box-default">
        <div class="box-header with-border">
                <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-primary pull-right']) ?>
        </div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout'=>'<div class="box-body no-padding table-responsive">{items}</div><div class="box-footer">{pager}</div>',
                'columns' => [
                    'id' => [
                        'label' => '№',
                        'format' => 'html',
                        'value' => function($data) {
                            return Html::a($data->id, ['view', 'id' => $data->id]);
                        },
                    ],
                    'name',
                    'number',
                ],
            ]); ?>
    </div>
</div>
