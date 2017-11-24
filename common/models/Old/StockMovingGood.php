<?php

namespace common\models\Old;

use Yii;

class StockMovingGood extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'stock_moving_goods';
    }

    public function getGoods()
    {
        return $this->hasOne(Good::className(), ['goods_id' => 'goods_id']);
    }
}
