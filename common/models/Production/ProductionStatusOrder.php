<?php

namespace common\models\Production;

use Yii;

class ProductionStatusOrder extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_status_order';
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

    public function getProductionOrders()
    {
        return $this->hasMany(ProductionOrder::className(), ['status' => 'id']);
    }
}
