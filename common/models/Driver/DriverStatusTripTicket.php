<?php

namespace common\models\Driver;

use Yii;

class DriverStatusTripTicket extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'driver_status_trip_tickets';
    }

    public function rules()
    {
        return [
            [['name', 'color'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'color' => 'Color',
        ];
    }

    public function getDriverTripTicket()
    {
        return $this->hasMany(DriverTripTicket::className(), ['status' => 'id']);
    }
}
