<?php
use yii\grid\GridView;
use yii\helpers\Html;

?>

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
                'action' => [
                    'visible' => ($statusTripTickets == 0 && Yii::$app->user->identity->checkRule('driver', 7)) ||
                        (in_array($statusTripTickets, [1, 2]) && Yii::$app->user->identity->checkRule('driver', 8)),
                    'format' => 'html',
                    'value' => function()
                    {
                        return '<div style="text-align: center; margin-top: 10px; white-space: nowrap;"><span class="glyphicon glyphicon-arrow-up"></span>
                                     <span class="glyphicon glyphicon-arrow-down"></span>
                                </div><div style="text-align: center; margin-top: 10px;"><span class="glyphicon glyphicon-remove"></span></div>';
                    }
                ],
                [
                    'class' => 'yii\grid\SerialColumn',
                    'header' => ''
                ],
                'id' => [
                    'label' => 'ID',
                    'format' => 'html',
                    'value' => function($trip)
                    {
                        $user = $trip->author;
                        $author = $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.';
                        $value = '<div><a href="/driver-trips/update?id='.$trip->id.'; ?>">'.$trip->id.'</a></div>
                                 <div>'.date('d.m.Y H:i', $trip->createdAt).'</div>
                                 <div style="white-space: nowrap;">'.$author.'</div>';
                        return $value;
                    }
                ],
                'typeOfTrip' => [
                    'label' => 'Тип',
                    'format' => 'html',
                    'value' => function($trip)
                    {
                        $value = '';
                        if ($trip->priority == 1) {
                            $value .= '<div><img src="/images/driver/fire.png" alt="фото"></div>';
                        }elseif ($trip->priority == 2) {
                            $value .= '<div><img src="/images/driver/warning.png" alt="фото"></div>';
                        }
                        $value .= '<div><img src="/images/driver/'.$trip->typeTrip->sign.'" alt="фото"> </div>';
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
                                  <div>'. round($trip->length * $trip->width * $trip->height, 2).' м3,</div>
                                  <div>'.$trip->weightOrder.' кг</div>';
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
                        <div><b>Желаемые:</b> <span style="white-space: nowrap;">
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
                        
                        $value .= '<div><b>Дата поездки:</b> '.($trip->dateTrip ? date('d.m.Y', $trip->dateTrip) : '-').'</div>';
                        
                        return $value;
                    }
                ],
                'notice' => [
                    'label' => 'Примечания',
                    'value' => function($trip)
                    {
                        return $trip->notice ? $trip->notice : 'нет';
                    }
                ],
                [
                    'attribute' => 'tripSuccess',
                    'visible' => $statusTripTickets == 3,
                    'label' => 'Поездка Успешна?',
                    'format' => 'raw',
                    'value' => function($trip)
                    {
                        if ($trip->status == 6) return '';
                        else return Html::button('ДА', ['class' => 'btn btn-success', 'name' => 'yes', 'value' => $trip->id]).''
                              .Html::button('НЕТ', ['class' => 'btn btn-danger', 'name' => 'no', 'value' => $trip->id]);
                    }
                ]
            ],
        ]); ?>
    </div>
</div>