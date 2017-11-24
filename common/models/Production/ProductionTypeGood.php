<?php

namespace common\models\Production;

use Yii;

class ProductionTypeGood extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_type_good';
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
        return $this->hasMany(ProductionOrder::className(), ['typeGood' => 'id']);
    }
}
