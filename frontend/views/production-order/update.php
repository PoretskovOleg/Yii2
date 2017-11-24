<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\assets\Production\ProductionAsset;

ProductionAsset::register($this);

$this->title = 'Редактирование заказ-наряда: ' . (empty($model->number) ? $model->id : $model->number);
$this->params['breadcrumbs'][] = ['label' => 'Реестр заказ-нарядов', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="production-order-update">

    <?= $this->render('_form', [
        'model' => $model,
        'goodSearchModel' => $goodSearchModel,
        'stages' => $stages,
        'selectStages' => $selectStages,
        'responsibles' => $responsibles,
        'usersOtk' => $usersOtk,
        'stagesFiles' => $stagesFiles,
        'maxQuantity' => $maxQuantity
    ]) ?>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">История:</h3>
        </div>
        <div class="box-body no-padding">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout'=>'<div class="box-body no-padding table-responsive">{items}</div>
                                   <div class="box-footer">{pager}</div>',
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered no-padding'
                ],
                'columns' => [
                    'status' => [
                        'label' => 'Статус',
                        'value' => function($history)
                        {
                            return $history->statusOrder->name;
                        }
                    ],
                    'createdAt' => [
                        'label' => 'Дата и время',
                        'value' => function($history)
                        {
                            return date('d.m.Y H:i', $history->createdAt);
                        }
                    ],
                    'author' => [
                        'label' => 'Автор',
                        'value' => function($history)
                        {
                            return $history->authorHistory->shortName;
                        }
                    ],
                    'comment' => [
                        'label' => 'Комментарий',
                        'value' => function($history)
                        {
                            return $history->comment;
                        }
                    ]
                ]
            ]); ?>
        </div>
    </div>
</div>
