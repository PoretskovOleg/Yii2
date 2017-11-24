<?php

namespace common\models\Driver;

use Yii;

class DriverTypeTrip extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'driver_type_trip';
    }

    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }
}
