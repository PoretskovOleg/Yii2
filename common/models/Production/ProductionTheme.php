<?php

namespace common\models\Production;

use Yii;

class ProductionTheme extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_theme';
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
        return $this->hasMany(ProductionOrder::className(), ['theme' => 'id']);
    }
}
