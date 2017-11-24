<?php

namespace common\models\Production;

use Yii;

class ProductionPriority extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_priority';
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

    public function getProductionOrders()
    {
        return $this->hasMany(ProductionOrder::className(), ['priority' => 'id']);
    }
}
