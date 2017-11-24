<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\assets\Driver\DriverAsset;
use yii\bootstrap\ActiveForm;

    DriverAsset::register($this);

$this->title = 'Реестр поездок';
$this->params['breadcrumbs'][] = $this->title;

$fieldOptions = [
    'template' => '
        <div class="col-sm-10 col-sm-offset-2">{error}</div>
        {label}
        <div class="col-sm-12 no-padding">{input}{hint}</div>
    ',
    'labelOptions' => ['class' => 'control-label col-sm-2'],
    'hintOptions' => ['class' => 'help-block']
];
?>
<div class="driver-trips-index">

    <div class="box box-info box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Поиск</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body no-padding">
            <?= $this->render('_search', [
                'model' => $searchModel,
                'typeOfTrip' => $typeOfTrip,
                'from' => $from,
                'to' => $to,
                'priority' => $priority,
                'region' => $region,
                'author' => $author,
                'status' => $status,
                'driver' => $driver,
                'car' => $car
            ]); ?>
        </div>
    </div>
    <div class="box box-info">
        <?php $formTrips = ActiveForm::begin([
                'successCssClass' => false,
                'method' => 'post'
            ]); ?>
        <div class="box-header with-border">
            <div class="col-sm-10 create_trip_ticket no-padding" hidden>
                <?php if (Yii::$app->user->identity->checkRule('driver', 7)) : ?>
                    <div class="col-sm-4 no-padding"><b>Выбрано: 
                        <span class="quantity">0</span> поездок, <span class="volume">0</span> м3, <span class="weight">0</span> кг</b></div>
                    <div class="col-sm-2 no-padding" style="min-width: 140px;">
                        <?= Html::submitButton('Создать путевой лист',
                            [
                                'class' => 'btn btn-primary',
                                'style' => 'padding: 6px 3px',
                                'name' => 'create_trip_ticket',
                                'value' => 'create_trip_ticket',
                                'formaction' => '/driver-trip-tickets/create'
                            ]); ?>
                    </div>
                    <div class="col-sm-6 no-padding" style="min-width: 430px;">
                        <div class="col-sm-5" style="text-align: right;">
                            <?= Html::submitButton('Добавить в путевой лист',
                            [
                                'class' => 'btn btn-primary',
                                'style' => 'padding: 6px 3px;',
                                'name' => 'add_trips',
                                'value' => 'add_trips',
                                'formaction' => '/driver-trip-tickets/add-trips'
                            ]); ?>
                        </div>
                        <div class="col-sm-7 no-padding" style="margin-top: -10px;">
                            <?= $formTrips->field($searchModel, 'tripTicketId', $fieldOptions)
                            ->dropdownList($tripTicket, ['prompt' => ''])->label(false); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-sm-12 no-padding">
                <?= Html::a('Создать поездку', ['create'], ['class' => 'btn btn-primary pull-right']) ?>
            </div>
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
                    'checkbox' => [
                        'format' => 'raw',
                        'value' => function($trip)use($formTrips, $searchModel)
                        {
                            if (in_array($trip->status, [2, 3, 4]) && $trip->tripTicketId == null)
                            return $formTrips->field($searchModel, 'trips[]', [
                                    'template' => '{input}'
                                ])->checkbox(['value' => $trip->id], false);
                            else return '';
                        }
                    ],
                    'id' => [
                        'label' => 'ID',
                        'format' => 'html',
                        'value' => function($trip)
                        {
                            $user = $trip->author;
                            $author = $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.';
                            $value = '<div><a href="./update?id='.$trip->id.'; ?>">'.$trip->id.'</a> </div>
                                     <div>'.date('d.m.Y H:i', $trip->createdAt).'</div>
                                     <div style="white-space: nowrap;">'.$author.'</div>';
                            return $value;
                        }
                    ],
                    'tripTicket' => [
                        'label' => '№ п.л., исполн.',
                        'format' => 'html',
                        'value' => function($trip)
                        {  
                            if (!empty($trip->tripTicketId)) {
                                $driver = $trip->tripTicket->driverTripTicket;
                                $value = '<div><a href="/driver-trip-tickets/update?id='.$trip->tripTicketId.'; ?>">'.$trip->tripTicketId.'</a></div><div class="'.$trip->tripTicket->statusTripTicket->color.
                                    '">'.$trip->tripTicket->statusTripTicket->name.'</div><div>'.
                                           ($driver->last_name . ' ' . mb_substr($driver->first_name, 0, 1) . '.' . mb_substr($driver->patronymic, 0, 1) . '.')
                                         . '</div><div>'.$trip->tripTicket->carTripTicket->name.'</div>';
                            } else $value = '';
                           return $value;
                        }
                    ],
                    'typeOfTrip' => [
                        'label' => 'Тип',
                        'format' => 'raw',
                        'value' => function($trip)
                        {
                            $value = '';
                            if ($trip->priority == 1) {
                                $value .= '<div><div class="sign" data-title="ОГОНЬ!!!"><img src="/images/driver/fire.png" alt="фото"></div></div>';
                            }elseif ($trip->priority == 2) {
                                $value .= '<div><div class="sign" data-title="Важно!"><img src="/images/driver/warning.png" alt="фото"></div></div>';
                            }
                            $value .= '<div><div class="sign" data-title="'.$trip->typeTrip->name.'"><img src="/images/driver/'.$trip->typeTrip->sign.'" alt="фото"> </div></div>';
                            return $value;
                        }
                    ],
                    'order' => [
                        'label' => 'Что везем',
                        'format' => 'html',
                        'value' => function($trip)
                        {
                            $value = '<div>'. 
                                ($trip->typeOfTrip == 1 ? 'Заказ' : $trip->typeTrip->name).
                                ($trip->typeOfTrip == 4 ? '' : ' № ' . $trip->orderNumber);
                            if ($trip->typeOfTrip != 4) {
                                $value .= '<span class="glyphicon glyphicon-shopping-cart"></span>';
                            }
                            $value .= '</div><div>'.$trip->subscribeOrder.'</div>';
                           return $value;
                        }
                    ],
                    'size' => [
                        'label' => 'Размеры',
                        'format' => 'html',
                        'value' => function($trip)
                        {
                            $value = '<div>'.$trip->length.'x'.$trip->width.'x'.$trip->height.'м,</div>
                                      <div><span>'. round($trip->length * $trip->width * $trip->height, 2).'</span> м3,</div>
                                      <div><span>'.$trip->weightOrder.'</span> кг</div>';
                            return $value;
                        }
                    ],
                    'from' => [
                        'label' => 'Откуда - От кого',
                        'format' => 'html',
                        'value' => function($trip)
                        {
                            $value = '<div><b>'.$trip->addressFrom->name .':</b> '.$trip->adressFrom.'</div>
                                      <div><b>От кого:</b> '.$trip->consignerName.', 
                                            ИНН '.$trip->consignerInn.', '.
                                            ($trip->consignerPhone ? 'тел.' . $trip->consignerPhone . ', ' : '').
                                            $trip->consignerUser.', тел.'.$trip->consignerUserPhone.
                                      '</div>';
                            return $value;
                        }
                    ],
                    'zoneFrom' => [
                        'label' => 'Р',
                        'value' => function($trip)
                        {
                            return $trip->zoneFrom;
                        }
                    ],
                    'to' => [
                        'label' => 'Куда - Кому',
                        'format' => 'html',
                        'value' => function($trip)
                        {
                            $value = '<div><b>'.$trip->addressTo->name .':</b> '.$trip->adressTo.'</div>';
                            if ($trip->terminalTC) {
                                $value .= '<div><b>Адрес в ТК:</b> '.$trip->terminalTC.'</div>';
                            }
                            $value .= '<div><b>Кому:</b> '.$trip->consigneeName.', 
                                            ИНН '.$trip->consigneeInn.', '.
                                            ($trip->consigneePhone ? 'тел.' . $trip->consigneePhone . ', ' : '').
                                            $trip->consigneeUser.', тел.'.$trip->consigneeUserPhone.
                                      '</div>';
                            return $value;
                        }
                    ],
                    'zoneTo' => [
                        'label' => 'Р',
                        'value' => function($trip)
                        {
                            return $trip->zoneTo;
                        }
                    ],
                    'status' => [
                        'label' => 'Статус',
                        'format' => 'html',
                        'value' => function($trip)
                        {
                            $value = '<div style="white-space: nowrap; text-align: center;" class="'.
                                $trip->statusTrip->color.'">'.$trip->statusTrip->name.'</div>';
                            return $value;
                        }
                    ],
                    'dates' => [
                        'label' => 'Даты',
                        'format' => 'html',
                        'value' => function($trip)
                        {
                            $value = 
                            '<div><b>Первичная:</b> '.date('d.m.Y', $trip->firstDate).'</div>
                            <div><b>Желаемые:</b> <span>
                                с '.($trip->desiredDateFrom ? date('d.m.Y', $trip->desiredDateFrom) : '-').
                                ' по '.($trip->desiredDateTo ? date('d.m.Y', $trip->desiredDateTo) : '-').'</span>
                            </div>';

                            if (!in_array($trip->status, [6, 7])) {
                                $time = $trip->dedline;
                                $timeNow = strtotime('now');
                                                           
                                if (($time - $timeNow) < 0 && abs($time - $timeNow) < 86400  )
                                    $value .= '<div class="red"> Сегодня </div>';
                                elseif (($time - $timeNow) < 0)
                                    $value .= '<div class="red"> - '.floor(($timeNow - $time) / 86400).' дн. </div>';
                                else
                                    $value .= '<div class="green">'.ceil(($time - $timeNow) / 86400).' дн. </div>';
                            }
                            
                            $value .= '<div style="color: red;"><b>Дата поездки:</b> '.($trip->dateTrip ? date('d.m.Y', $trip->dateTrip) : '-').'</div>';
                            
                            return $value;
                        }
                    ],
                    'notice' => [
                        'label' => 'Примечания',
                        'value' => function($trip)
                        {
                            return $trip->notice ? $trip->notice : 'нет';
                        }
                    ]
                ],
            ]); ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
