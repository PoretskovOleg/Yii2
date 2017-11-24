<?php
    use common\models\Driver\DriverAddress;
    use frontend\assets\Driver\DriverAsset;

    DriverAsset::register($this);
    $this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU');

    $this->title = in_array($tripTicket->status, [3, 4]) ? 'Путевой лист № ' . $tripTicket->id . ' от ' . date('d.m.Y', $tripTicket->departureDate) : 'Предварительный просмотр';
    $this->params['breadcrumbs'][] = ['label' => 'Реестр путевых листов', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => 'Редактирование путевого листа', 'url' => ['update?id='.$tripTicket->id]];
    $this->params['breadcrumbs'][] = in_array($tripTicket->status, [3, 4]) ? 'Печатная форма путевого листа' : 'Предварительный просмотр';
    $num = 0;
?>

<div class="print box box-defult">
    <div class="box-header ">
<div <?=(in_array($tripTicket->status, [3, 4]) ? "" : "hidden");?> >
        <div id="print" style="float:right; margin: 5px">
            <button class="btn btn-primary" onclick="printTripTicket('<?=$this->title; ?>')">Печать</button>
        </div>
        <div>
            <span class="header_trip">Водитель-экспедитор:</span> 
            <?php
                $user = $tripTicket->driverTripTicket;
                echo $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.';
            ?>
        </div>
        <div>
            <span class="header_trip">Автомобиль:</span> <?=$tripTicket->carTripTicket->name . ', номер ' . $tripTicket->carTripTicket->number;?>
        </div>
        <div>
            <span class="header_trip">Менеджер:</span>
            <?php
                $user = $tripTicket->authorTripTicket;
                echo $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '., тел. ' . $user->phone_number;
            ?>
        </div>
        <div>
            <span class="header_trip">Место выезда:</span> <?=$tripTicket->addressStart->name; ?>,
            <span class="address"><?=$tripTicket->addressStart->address; ?></span>
        </div>
</div>
        <div>
            <?php 
                $timeLoad = 0;
                $weightStart = 0;
                foreach ($trips as $item) {
                    if ($item->position >= $trips[0]->position && $item->from == $tripTicket->departurePlace) {
                        $timeLoad += $item->timeLoad;
                        $weightStart += $item->weightOrder;
                        if ($item->to == $tripTicket->departurePlace) break;
                    }
                }
            ?>
            <b>Время прихода на работу: <span class="time_start"><?=date('H:i', $tripTicket->departureDate - $timeLoad*60); ?></span> </b>
        </div>
    </div>
    <div class="box-body no-padding">
        <table class="table table-bordered table-striped no-padding">
            <thead>
                <tr>
                    <th>№ поездки</th>
                    <th>Тип</th>
                    <th>Что везем</th>
                    <th>Размеры</th>
                    <th>Время</th>
                    <th>План</th>
                    <th>Адрес</th>
                <?php if (in_array($tripTicket->status, [3, 4])) : ?>
                    <th>Примечания</th>
                    <th>Подпись</th>
                <?php endif;?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3" class="grey" style="text-align: left;"><b>СТАРТ время выезда: <span class="time_out"><?=date('H:i', $tripTicket->departureDate); ?></span></b></td>
                    <td colspan="3" class="grey"><b><?=$tripTicket->addressStart->name; ?></b></td>
                </tr>
                <?php $inputAll = array(); $outputAll = array(); ?>
                <?php
                    $input = array();
                    $timeLoad = 0;
                    foreach ($trips as $item) {
                        if ($item->from == $tripTicket->departurePlace) {
                            $input[] = $item->id;
                            $timeLoad += $item->timeLoad;
                            $inputAll[] = $item->id;
                        }
                    }
                ?>
                <?php if (!empty($input)) : ?>
                    <tr>
                        <td colspan="3" class="green">Загрузить поездки: <?=implode( ', ', $input); ?></td>
                        <td colspan="2" class="green">Время загрузки: <?=$timeLoad; ?>мин.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($trips as $trip) : ?>

                    <?php if ( in_array($trip->from, $points) && !in_array($trip->id, $inputAll) ) : ?>

                        <?php
                            $input = array();
                            $timeLoad = 0;
                            foreach ($trips as $item) {
                                if ($item->position >= $trip->position && $item->from == $trip->from && !in_array($item->id, $inputAll)) {
                                    $input[] = $item->id;
                                    $timeLoad += $item->timeLoad;
                                    $inputAll[] = $item->id;
                                }
                            }
                            $output = array();
                            $timeUnload = 0;
                            foreach ($trips as $item) {
                                if ($item->position < $trip->position && $item->to == $trip->from && !in_array($item->id, $outputAll)) {
                                    $output[] = $item->id;
                                    $timeUnload += $item->timeUnload;
                                    $outputAll[] = $item->id;
                                }
                            }
                        ?>
                        <tr>
                            <td colspan="3" class="blue"> Промежуточная точка</td>
                            <td class="blue">
                                <?=$trip->addressFrom->name; ?>
                                <span class="address" hidden><?=$trip->adressFrom; ?>
                                    <span class="time_load"><?=($timeLoad + $timeUnload); ?></span>
                                </span>
                            </td>
                            <td style="padding: 8px 0 0 3px; background-color: rgb(50, 100, 255); color: white;">
                                 <span class="time_in"></span> – <span class="time_out"></span>
                            </td>
                            <td class="blue">
                                <div hidden>Расст. <span class="distance"><?php $num++; echo (!empty($traffic) ? $traffic[$num]->distance : '-'); ?></span> км,</div> 
                                <div>
                                    Путь <span class="duration"><?=(!empty($traffic) ? $traffic[$num]->duration : '-'); ?></span>
                                    <div style="width: 80px;" hidden><input type="number" name="duration"> <button type="button" name="duration" class="btn-success">ОК</button></div> мин
                                </div>
                            </td>
                        </tr>
                        <?php if (!empty($output)) : ?>
                            <tr>
                                <td colspan="3" class="yellow">Выгрузить поездки: <?=implode( ', ', $output); ?></td>
                                <td colspan="2" class="yellow">Время выгрузки: <?=$timeUnload; ?>мин.</td>
                            </tr>
                        <?php endif; ?>

                        <?php if (!empty($input)) : ?>
                            <tr>
                                <td colspan="3" class="green">Загрузить поездки: <?=implode( ', ', $input); ?></td>
                                <td colspan="2" class="green">Время загрузки: <?=$timeLoad; ?>мин.</td>
                            </tr>
                        <?php endif; ?>
                        
                        <tr>
                            <td style="padding: 2px 0 0 3px;">
                                <div><?=$trip->id; ?></div>
                                <div><?=date('d.m.Y H:i', $trip->createdAt);?></div>
                                <div><?php $user = $trip->author;
                                echo $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.'; ?></div>
                            </td>
                            <td>
                                <img src="/images/driver/<?=$trip->typeTrip->sign; ?>" alt="фото">
                            </td>
                            <td style="padding: 2px 0 0 3px;">
                                <?=($trip->typeOfTrip == 1 ? 'Заказ' : $trip->typeTrip->name).($trip->typeOfTrip == 4 ? '' : ' № ' . $trip->orderNumber); ?>
                                <div><?=$trip->subscribeOrder; ?></div>
                                <?php if (!empty($goods[$trip->id])) : ?>
                                    <?php foreach ($goods[$trip->id] as $good) : ?>
                                        <div>- <?=$good['id']?> * <?=$good['name']?> * <?=$good['amount']?> * <?=$good['unit']?></div>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </td>
                            <td style="padding: 2px 0 0 3px;">
                                <div><?=$trip->length.'x'.$trip->width.'x'.$trip->height; ?>м,</div>
                                <div><?=round($trip->length * $trip->width * $trip->height, 2); ?>м3,</div>
                                <div><?=$trip->weightOrder; ?>кг</div>
                            </td>
                            <td style="padding: 2px 0 0 3px;">
                                <span class="time_in"></span> – <span class="time_out"></span>
                            </td>
                            <td style="padding: 2px 0 0 3px;">
                                <div>Расст <span class="distance"><?php $num++; echo (!empty($traffic) ? $traffic[$num]->distance : '-'); ?></span> км,</div>
                                <div>
                                    Путь <span class="duration"><?=(!empty($traffic) ? $traffic[$num]->duration : '-'); ?></span>
                                    <div style="width: 80px;" hidden><input type="number" name="duration"> <button type="button" name="duration" class="btn-success">ОК</button></div> мин,
                                </div>
                                <div>
                                    Разгр <span class="time_load"><?=$trip->timeUnload ?></span> мин
                                </div>
                            </td>
                            <td style="padding: 2px 0 0 3px;">
                                <?php if (in_array($trip->id, $inputAll)) {
                                    $outputAll[] = $trip->id;
                                    echo 
                                        '<div><b>'.$trip->addressTo->name .':</b><span class="address">'.$trip->adressTo.'</span></div>'.
                                        ($trip->terminalTC != null ? '<div><b>Адрес в ТК:</b> '.$trip->terminalTC.'</div>' : '')
                                        .'<div><b>Кому:</b> '.$trip->consigneeName.',
                                            ИНН '.$trip->consigneeInn.', '.
                                            ($trip->consigneePhone ? 'тел.' . $trip->consigneePhone . ', ' : '').
                                            $trip->consigneeUser.', тел.'.$trip->consigneeUserPhone.
                                        '</div>';
                                    } else {
                                        $inputAll[] = $trip->id;
                                        echo 
                                        '<div><b>'.$trip->addressFrom->name .':</b><span class="address">'.$trip->adressFrom.'</span></div>
                                         <div><b>От кого:</b> '.$trip->consignerName.', 
                                            ИНН '.$trip->consignerInn.', '.
                                            ($trip->consignerPhone ? 'тел.' . $trip->consignerPhone . ', ' : '').
                                            $trip->consignerUser.', тел.'.$trip->consignerUserPhone.
                                        '</div>';
                                    }
                                ?>
                            </td>
                        <?php if (in_array($tripTicket->status, [3, 4])) : ?>
                            <td style="padding: 2px 0 0 3px;"><?=$trip->notice;?></td>
                            <td style="padding: 2px 0 0 3px;"><div>_______</div><div>Время:</div><div>___:___</div></td>
                        <?php endif;?>
                        </tr>
                    <?php else : ?>
                        <tr>
                            <td style="padding: 2px 0 0 3px;"> 
                                <div><?=$trip->id; ?></div>
                                <div><?=date('d.m.Y H:i', $trip->createdAt);?></div>
                                <div><?php $user = $trip->author;
                                echo $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.'; ?></div>
                            </td>
                            <td>
                                <img src="/images/driver/<?=$trip->typeTrip->sign; ?>" alt="фото">
                            </td>
                            <td style="padding: 2px 0 0 3px;">
                                <?=($trip->typeOfTrip == 1 ? 'Заказ' : $trip->typeTrip->name).($trip->typeOfTrip == 4 ? '' : ' № ' . $trip->orderNumber); ?>
                                <div><?=$trip->subscribeOrder; ?></div>
                                <?php if (!empty($goods[$trip->id])) : ?>
                                    <?php foreach ($goods[$trip->id] as $good) : ?>
                                        <div>- <?=$good['id']?> * <?=$good['name']?> * <?=$good['amount']?> * <?=$good['unit']?></div>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </td>
                            <td style="padding: 2px 0 0 3px;">
                                <div><?=$trip->length.'x'.$trip->width.'x'.$trip->height; ?>м,</div>
                                <div><?=round($trip->length * $trip->width * $trip->height, 2); ?>м3,</div>
                                <div><?=$trip->weightOrder; ?>кг</div>
                            </td>
                            <td style="padding: 2px 0 0 3px;">
                                <span class="time_in"></span> – <span class="time_out"></span>
                            </td>
                            <td style="padding: 2px 0 0 3px;">
                                <div>Расст <span class="distance"><?php $num++; echo (!empty($traffic) ? $traffic[$num]->distance : '-'); ?></span> км,</div>
                                <div>
                                    Путь <span class="duration"><?=(!empty($traffic) ? $traffic[$num]->duration : '-'); ?></span>
                                    <div style="width: 80px;" hidden><input type="number" name="duration"> <button type="button" name="duration" class="btn-success">ОК</button></div> мин,
                                </div>
                                <div>
                                    <?php if (in_array($trip->from, $points)) : ?>
                                        Разгр <span class="time_load"><?=$trip->timeUnload ?></span> мин
                                    <?php else : ?>
                                        Загр <span class="time_load"><?=$trip->timeLoad ?></span> мин
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 2px 0 0 3px;">
                                <?php if (in_array($trip->id, $inputAll)) {
                                    $outputAll[] = $trip->id;
                                    echo 
                                        '<div><b>'.$trip->addressTo->name .':</b><span class="address">'.$trip->adressTo.'</span></div>'.
                                        ($trip->terminalTC != null ? '<div><b>Адрес в ТК:</b> '.$trip->terminalTC.'</div>' : '')
                                        .'<div><b>Кому:</b> '.$trip->consigneeName.', 
                                            ИНН '.$trip->consigneeInn.', '.
                                            ($trip->consigneePhone ? 'тел.' . $trip->consigneePhone . ', ' : '').
                                            $trip->consigneeUser.', тел.'.$trip->consigneeUserPhone.
                                        '</div>';
                                    } 
                                    else {
                                        $inputAll[] = $trip->id;
                                        echo 
                                        '<div><b>'.$trip->addressFrom->name .':</b><span class="address">'.$trip->adressFrom.'</span></div>
                                         <div><b>От кого:</b> '.$trip->consignerName.', 
                                            ИНН '.$trip->consignerInn.', '.
                                            ($trip->consignerPhone ? 'тел.' . $trip->consignerPhone . ', ' : '').
                                            $trip->consignerUser.', тел.'.$trip->consignerUserPhone.
                                        '</div>';
                                    }
                                ?>
                            </td>
                        <?php if (in_array($tripTicket->status, [3, 4])) : ?>
                            <td style="padding: 2px 0 0 3px;"><?=$trip->notice;?></td>
                            <td style="padding: 2px 0 0 3px;"><div>_______</div><div>Время:</div><div>___:___</div></td>
                        <?php endif;?>
                        </tr>
                    <?php endif; ?>

                <?php endforeach; ?>
                <?php
                    foreach ($points as $key => $point) {
                        if ($point == $tripTicket->finishPlace) {
                            unset($points[$key]);
                            break;
                        }
                    }
                ?>
                <?php foreach ($points as $checkpoint) : ?>
                    <?php $output = array(); $timeUnload = 0; ?>
                    <?php foreach ($trips as $trip) : ?>
                        <?php if ($trip->to == $checkpoint && !in_array($trip->id, $outputAll)) : ?>
                            <?php $output[] = $trip->id; $timeUnload += $trip->timeUnload; ?>
                            <?php $outputAll[] = $trip->id; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (!empty($output)) : ?>
                        <tr>
                            <td colspan="3" class="blue">Промежуточная точка</td>
                            <td class="blue">
                                <?=DriverAddress::findOne($checkpoint)->name; ?>, 
                                <span class="address" hidden><?=DriverAddress::findOne($checkpoint)->address; ?> 
                                    <span class="time_load"><?=$timeUnload; ?></span>
                                </span>
                            </td>
                            <td style="padding: 8px 0 0 3px; background-color: rgb(50, 100, 255); color: white;">
                                 <span class="time_in"></span> – <span class="time_out"></span>
                            </td>
                            <td class="blue">
                                <div hidden>Расст. <span class="distance"><?php $num++; echo (!empty($traffic) ? $traffic[$num]->distance : '-'); ?></span> км,</div> 
                                <div>
                                    Путь <span class="duration"><?=(!empty($traffic) ? $traffic[$num]->duration : '-'); ?></span>
                                    <div style="width: 80px;" hidden><input type="number" name="duration"> <button type="button" name="duration" class="btn-success">ОК</button></div> мин
                                </div>
                            </td>

                        </tr>
                        <tr>
                            <td colspan="3" class="yellow">Вызгрузить поездки: <?=implode( ', ', $output); ?></td>
                            <td colspan="2" class="yellow">Время выгрузки: <?=$timeUnload; ?>мин.</td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php $output = array(); $timeUnload = 0;
                    foreach ($trips as $trip) {
                        if ($trip->to == $tripTicket->finishPlace && !in_array($trip->id, $outputAll)) {
                            $output[] = $trip->id;
                            $outputAll[] = $trip->id; $timeUnload += $trip->timeUnload;
                        }
                    } ?>

                <tr>
                    <td colspan="3" class="grey" style="text-align: left;"><b>ФИНИШ</b></td>
                    <td class="grey">
                        <?=DriverAddress::findOne($tripTicket->finishPlace)->name; ?>
                        <span class="address" hidden><?=DriverAddress::findOne($tripTicket->finishPlace)->address; ?>
                            <span class="time_load"><?=$timeUnload; ?></span>
                        </span>
                    </td>
                    <td style="padding: 8px 0 0 3px; background-color: lightgrey; color: black;">
                        <span class="time_in"></span> – <span class="time_out"></span>
                    </td>
                    <td class="grey">
                        <div hidden>
                            Расст. <span class="distance"><?php $num++; echo (!empty($traffic) ? $traffic[$num]->distance : '-'); ?></span> км,
                        </div>
                        <div>
                            Путь <span class="duration"><?=(!empty($traffic) ? $traffic[$num]->duration : '-'); ?></span>
                            <div style="width: 80px;" hidden><input type="number" name="duration"> <button type="button" name="duration" class="btn-success">ОК</button></div> мин
                         </div>
                    </td>
                </tr>

                
                <?php if (!empty($output)) : ?>
                    <tr>
                        <td colspan="3" class="yellow">Вызгрузить поездки: <?=implode( ', ', $output); ?></td>
                        <td colspan="2" class="yellow">Время выгрузки: <?=$timeUnload; ?> мин.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="box box-defult" hidden>
    <div id="map" class="map" style="width: 1200px; height: 600px"></div>
</div>

<?php $weightFinish = 0;
foreach ($trips as $trip) {
    if ($trip->to == $tripTicket->finishPlace) {
        $weightFinish += $trip->weightOrder;
    }
} ?>

<div class="box box-defult">
    <div class="box-footer" style="font-size: 12px;">
        <div><b>Итого:</b></div>
        <div><span class="footer_print">Масса выезда, кг:</span> <?=$weightStart?></div>
        <div><span class="footer_print">Масса прибытия, кг:</span> <?=$weightFinish?></div>
        <div><span class="footer_print">Точек, шт:</span> <span class="points_all"></span></div>
        <div><span class="footer_print">Расстояние, км:</span> <span class="distance_all"></span></div>
        <div><span class="footer_print">Время расчетное в пути, ч:мин:</span> <span class="duration_all"></span></div>
        <div><span class="footer_print">Время расчетное прибытия на базу, ч:мин:</span> <span class="time_in"></span></div>
    <?php if(in_array($tripTicket->status, [3, 4]) ) : ?>
        <div style="margin-top: 20px;">
            С маршрутом и задачами ознакомлен, маршрут понятен, вопросов нет________________подпись водителя (время ознакомления_____________)
        </div>
        <div>Причины отклонения от маршрута_________________________________________________________________________________________________</div>
        <div>Подпись начальника_____________________________________________________________________________________________________________</div>
    <?php endif; ?>
    </div>
</div>