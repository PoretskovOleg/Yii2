<?php

namespace common\models\Production;

use Yii;

class ProductionOrderPlanning extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_order_planning';
    }

    public function rules()
    {
        return [
            [['order', 'stage', 'status'], 'integer'],
            [['order'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionOrder::className(), 'targetAttribute' => ['order' => 'id']],
            [['stage'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionStageOrder::className(), 'targetAttribute' => ['stage' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => 'Order',
            'stage' => 'Stage',
            'status' => 'Status',
        ];
    }

    public function getOrder0()
    {
        return $this->hasOne(ProductionOrder::className(), ['id' => 'order']);
    }

    public function getNameStage()
    {
        return $this->hasOne(ProductionStageOrder::className(), ['id' => 'stage']);
    }
}
