<?php

namespace common\models\Old;

use Yii;

class OrderLab extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'order_lab';
    }

    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['list_unit_id' => 'unit_id']);
    }
}
