<?php

namespace common\models\Driver;

use common\models\User;
use Yii;

class DriverTrip extends \yii\db\ActiveRecord
{
    public $priorityStart;
    
    public static function tableName()
    {
        return 'driver_trips';
    }

    public function rules()
    {
        return [
            [
                [
                    'typeOfTrip', 'priority', 'subscribeOrder', 'weightOrder', 
                    'timeLoad', 'timeUnload', 'consignerName', 'consignerUserPhone',
                    'firstDate', 'from', 'zoneFrom', 
                    'consignerUser', 'length', 'width', 'height',
                    'to', 'zoneTo', 'consigneeName',
                    'consigneeUserPhone', 'consigneeUser'
                ], 'required'
            ],
            [
                ['adressFrom', 'adressTo'], 'required', 'message' => 'Заполните поле "Адрес" правильно!'
            ],
            [
                [
                    'status', 'typeOfTrip', 'priority',
                    'timeLoad', 'from', 'zoneFrom',
                    'to', 'zoneTo', 'authorId', 'tripTicketId',
                ], 'integer'
            ],
            [
                'orderNumber', 'match', 'pattern' => '/^([C,T,С,Т]-)?\d{4,5}$/',
                'when' => function ($model) {
                    return $model->typeOfTrip == 1;
                }, 'whenClient' => "function (attribute, value) {
                    return $('select#drivertrips-typeoftrip').val() == 1;
                }"
            ],
            [
                [
                    'weightOrder', 'length', 'width', 'height'
                ], 'number', 'numberPattern' => '/^\s*[0-9]*[.,]?[0-9]+\s*$/'
            ],
            [
                [
                    'notice', 'subscribeOrder', 'adressFrom',
                    'consignerName', 'consignerPhone', 'consignerUser',
                    'consignerUserPhone', 'consignerInn', 'consigneeName',
                    'consigneePhone', 'consigneeUser', 'consigneeUserPhone',
                    'consigneeInn', 'adressTo', 'terminalTC', 
                ], 'string', 'max' => 255
            ],
            [
                'orderNumber', 'string', 'max' => 10
            ],
            [
                [
                    'firstDate', 'desiredDateFrom', 'position',
                    'desiredDateTo', 'dateTrip', 'dedline', 'createdAt'
                ], 'safe'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
                'typeOfTrip' => 'Тип поездки',
                'priority' => 'Приоритет',
                'notice' => 'Важные примечания',
                'orderNumber' => 'Номер заказа',
                'subscribeOrder' => 'Что везем',
                'weightOrder' => 'Масса, кг',
                'timeLoad' => 'Время загрузки, мин',
                'timeUnload' => 'Время выгрузки, мин',
                'length' => 'Размеры ДхШхВ, м',
                'width' => 'Ширина',
                'height' => 'Высота',
                'firstDate' => 'Первичная дата',
                'from' => 'Откуда забираем',
                'adressFrom' => 'Адрес',
                'zoneFrom' => 'Зона Москвы откуда',
                'consignerName' => 'Отправитель' ,
                'consignerPhone' => 'Тел.',
                'consignerUser' => 'Конт. лицо',
                'consignerUserPhone' => 'Тел.',
                'consignerInn' => 'ИНН',
                'to' => 'Куда везем',
                'adressTo' => 'Адрес',
                'zoneTo' => 'Зона Москвы куда',
                'consigneeName' => 'Получатель' ,
                'consigneePhone' => 'Тел.',
                'consigneeUser' => 'Конт. лицо',
                'consigneeUserPhone' => 'Тел.',
                'consigneeInn' => 'ИНН',
                'terminalTC' => 'Куда отправляем в ТК',
                'desiredDateFrom' => 'Желаемые даты',
                'dateTrip' => 'Дата поездки'
            ];
    }

    public function beforeValidate()
    {
        $this->length = str_replace(',', '.', $this->length);
        $this->width = str_replace(',', '.', $this->width);
        $this->height = str_replace(',', '.', $this->height);
        $this->weightOrder = str_replace(',', '.', $this->weightOrder);

        if ($this->typeOfTrip == 1) {
            $orderNum = explode('-', $this->orderNumber);
            if ($orderNum[0] === 'T' || $orderNum[0] === 'Т') $this->orderNumber = 'T-'.$orderNum[1];
            elseif ($orderNum[0] === 'С' || $orderNum[0] === 'C') $this->orderNumber = 'C-'.$orderNum[1];
            else $this->orderNumber = $orderNum[0];
        }

        $this->firstDate =
            (!empty($this->firstDate) && !is_numeric($this->firstDate)) ?
            date_create_from_format('d.m.Y H:i:s', $this->firstDate . ' 00:00:01')->format('U') : $this->firstDate;
        $this->dedline =
            (!empty($this->dedline) && !is_numeric($this->dedline)) ?
            date_create_from_format('d.m.Y H:i:s', $this->dedline . ' 00:00:01')->format('U') : $this->dedline;
        $this->desiredDateFrom =
            (!empty($this->desiredDateFrom) && !is_numeric($this->desiredDateFrom) ) ?
            date_create_from_format('d.m.Y H:i:s', $this->desiredDateFrom.' 00:00:01')->format('U') : $this->desiredDateFrom;
        $this->desiredDateTo =
            (!empty($this->desiredDateTo) && !is_numeric($this->desiredDateTo))  ?
            date_create_from_format('d.m.Y H:i:s', $this->desiredDateTo.' 00:00:01')->format('U') : $this->desiredDateTo;
        $this->dateTrip =
            (!empty($this->dateTrip) && !is_numeric($this->dateTrip) ) ?
            date_create_from_format('d.m.Y H:i:s', $this->dateTrip.' 00:00:01')->format('U') : $this->dateTrip;
        return parent::beforeValidate();
    }

    public function getTypeTrip()
    {
        return $this->hasOne(DriverTypeTrip::className(), ['id' => 'typeOfTrip']);
    }

    public function getStatusTrip()
    {
        return $this->hasOne(DriverStatusTrip::className(), ['id' => 'status']);
    }

    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['user_id' => 'authorId'])
            ->select(['user.last_name', 'user.first_name', 'user.patronymic']);
    }

    public function getAddressFrom()
    {
        return $this->hasOne(DriverAddress::className(), ['id' => 'from']);
    }

    public function getAddressTo()
    {
        return $this->hasOne(DriverAddress::className(), ['id' => 'to']);
    }

    public function getTripTicket()
    {
        return $this->hasOne(DriverTripTicket::className(), ['id' => 'tripTicketId']);
    }
}
?>