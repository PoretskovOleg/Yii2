<?php

namespace common\models\Driver;

use Yii;

class DriverCar extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'driver_car';
    }

    public function rules()
    {
        return [
            [['name', 'number'], 'required'],
            [['name', 'number'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование',
            'number' => 'Гос. номер',
        ];
    }
}
