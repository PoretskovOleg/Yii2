<?php

namespace common\models\Driver;

use Yii;

class DriverAddress extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'driver_address';
    }

    public function rules()
    {
        return [
            [['name', 'address', 'region'], 'required'],
            [['from', 'to', 'tk', 'region'], 'integer'],
            [['name', 'address'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Обозначение',
            'address' => 'Адрес',
            'from' => 'Пункт загрузки',
            'to' => 'Пункт разгрузки',
            'tk' => 'Транспортная компания',
            'region' => 'Район Москвы'
        ];
    }
}
