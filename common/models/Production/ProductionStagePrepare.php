<?php

namespace common\models\Production;

use Yii;

class ProductionStagePrepare extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_stage_prepare';
    }

    public function rules()
    {
        return [
            [['name', 'shortName'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'shortName' => 'Short Name',
        ];
    }

    public function getProductionPrepareOrders()
    {
        return $this->hasMany(ProductionPrepareOrder::className(), ['stage' => 'id']);
    }
}
