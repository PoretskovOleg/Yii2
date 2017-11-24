<?php

namespace common\models\Driver;

use Yii;

class DriverTraffic extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'driver_traffic';
    }

    public function rules()
    {
        return [
            [['trip_ticket', 'number', 'duration', 'distance'], 'integer'],
            [['address_start', 'address_finish'], 'string', 'max' => 255],
            [['trip_ticket'], 'exist', 'skipOnError' => true, 'targetClass' => DriverTripTicket::className(), 'targetAttribute' => ['trip_ticket' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trip_ticket' => 'Trip Ticket',
            'number' => 'Number',
            'duration' => 'Duration',
            'distance' => 'Distance',
            'address_start' => 'Address Start',
            'address_finish' => 'Address Finish',
        ];
    }

    public function getTripTicket()
    {
        return $this->hasOne(DriverTripTicket::className(), ['id' => 'trip_ticket']);
    }
}
