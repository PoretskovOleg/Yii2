<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\assets\Driver\DriverAsset;

    DriverAsset::register($this);

$this->title = 'Реестр путевых листов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="driver-trip-tickets-index">
    <div class="box box-info">
            <?= $this->render('_search', [
                'model' => $searchModel,
                'status' => $status,
                'driver' => $driver,
                'car' => $car
            ]);?>
    </div>

    <div class="box box-info">
        <div class="box-body no-padding">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout'=>'<div class="box-body no-padding table-responsive">{items}</div>
                            <div class="box-footer">{pager}</div>',
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered'
                ],
                'columns' => [
                    'id' => [
                        'label' => 'ID',
                        'format' => 'html',
                        'value' => function($tripTicket)
                        {
                            return '<a href="./update?id='.$tripTicket->id.'; ?>">'.$tripTicket->id.'</a>';
                        }
                    ],
                    'createdAt' => [
                        'label' => 'Время создания',
                        'value' => function($tripTicket)
                        {
                            return date('d.m.Y H:i', $tripTicket->createdAt);
                        }
                    ],
                    'driver' => [
                        'label' => 'Водитель',
                        'value' => function($tripTicket)
                        {
                            $driver = $tripTicket->driverTripTicket;
                            return $driver->last_name . ' ' .
                                mb_substr($driver->first_name, 0, 1) . '.' .
                                mb_substr($driver->patronymic, 0, 1) . '.';
                        }
                    ],
                    'car' => [
                        'label' => 'Автомобиль',
                        'value' => function($tripTicket)
                        {
                            return $tripTicket->carTripTicket->name;
                        }
                    ],
                    'author' => [
                        'label' => 'Автор',
                        'value' => function($tripTicket)
                        {
                            $author = $tripTicket->authorTripTicket;
                            return $author->last_name . ' ' .
                                mb_substr($author->first_name, 0, 1) . '.' .
                                mb_substr($author->patronymic, 0, 1) . '.';
                        }
                    ],
                    'status' => [
                        'label' => 'Статус',
                        'format' => 'html',
                        'value' => function($tripTicket)
                        {
                            return '<div style="white-space: nowrap; text-align: center;" class="'.
                                    $tripTicket->statusTripTicket->color.'">'.$tripTicket->statusTripTicket->name.'</div>';
                        }
                    ],
                    'departureDate' => [
                        'label' => 'Плановое время выезда',
                        'value' => function($tripTicket)
                        {
                            return date('d.m.Y H:i', $tripTicket->departureDate);
                        }
                    ]
                ],
            ]); ?>
        </div>
    </div>
</div>
