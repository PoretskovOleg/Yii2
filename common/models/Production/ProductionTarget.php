<?php

namespace common\models\Production;

use Yii;

class ProductionTarget extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_target';
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
        return $this->hasMany(ProductionOrder::className(), ['target' => 'id']);
    }
}
