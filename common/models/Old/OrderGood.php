<?php

namespace common\models\Old;

use Yii;

class OrderGood extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'order_goods';
    }

    public function getGood()
    {
        return $this->hasOne(Good::className(), ['goods_id' => 'goods_id']);
    }

    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['list_unit_id' => 'unit_id']);
    }

    public function getWeight()
    {
        return $this->hasOne(Good::className(), ['goods_id' => 'goods_id']);
    }
}
