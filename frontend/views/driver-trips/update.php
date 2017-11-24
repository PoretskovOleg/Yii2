<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->params['breadcrumbs'][] = ['label' => 'Реестр поездок', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="driver-trips-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelComment' => $modelComment,
        'edit' => $edit,
        'isAddressFrom' =>$isAddressFrom,
        'isAddressTo' => $isAddressTo
    ]); ?>

    <div class="box box-default box-solid">
        <div class="box-header">
            <h3 class="box-title">Статусы и комментарии</h3>
        </div>
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout'=>'<div class="box-body no-padding table-responsive">{items}</div><div class="box-footer">{pager}</div>',
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered'
                ],
                'columns' => [
                    'status' => [
                        'label' => 'Статус',
                        'value' => function($history)
                        {
                            return $history->statusTrip->name;
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
                            $user = $history->authorHistory;
                            return $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.';
                        }
                    ],
                    'comment' => [
                        'label' => 'Комментарий',
                        'value' => function($history)
                        {
                            return $history->comment;
                        }
                    ]
                ],
            ]); ?>
        </div>
    </div>
</div>
