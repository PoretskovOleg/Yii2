<?php

namespace common\models\Driver;

use Yii;
use common\models\User;

class DriverTripTicket extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'driver_trip_tickets';
    }

    public function rules()
    {
        return [
            [['departurePlace', 'departureDate', 'driver', 'car', 'finishPlace'], 'required'],
            [['createdAt', 'status', 'driver', 'car', 'author', 'departurePlace', 'finishPlace'], 'integer'],
            [['departureDate', 'id'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'createdAt' => 'Created At',
            'status' => 'Status',
            'driver' => 'Водитель-экспедитор',
            'car' => 'Автомобиль',
            'author' => 'Author',
            'departureDate' => 'Дата поездки',
            'departurePlace' => 'Место выезда',
            'finishPlace' => 'Финиш'
        ];
    }

    public function beforeValidate()
    {
        $this->departureDate =
            (!empty($this->departureDate) && !is_numeric($this->departureDate)) ?
            date_create_from_format('d.m.Y H:i:s', $this->departureDate . ':00')->format('U') : $this->departureDate;
        return parent::beforeValidate();
    }

    public function getCarTripTicket()
    {
        return $this->hasOne(DriverCar::className(), ['id' => 'car']);
    }

    public function getStatusTripTicket()
    {
        return $this->hasOne(DriverStatusTripTicket::className(), ['id' => 'status']);
    }

    public function getDriverTripTicket()
    {
        return $this->hasOne(User::className(), ['user_id' => 'driver'])
            ->select(['user.last_name', 'user.first_name', 'user.patronymic']);
    }

    public function getAuthorTripTicket()
    {
        return $this->hasOne(User::className(), ['user_id' => 'author'])
            ->select(['user.last_name', 'user.first_name', 'user.patronymic', 'user.phone_number']);
    }

    public function getTrips()
    {
       return $this->hasMany(DriverTrip::className(), ['tripTicketId' => 'id']);
    }

    public function getAddressStart()
    {
        return $this->hasOne(DriverAddress::className(), ['id' => 'departurePlace']);
    }

    public function getAddressFinish()
    {
        return $this->hasOne(DriverAddress::className(), ['id' => 'finishPlace']);
    }
}
