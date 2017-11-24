<?php

namespace common\models\Old;

use Yii;

class OrderSiz extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'order_siz';
    }

    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['list_unit_id' => 'unit_id']);
    }
}
