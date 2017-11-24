<?php

namespace common\models\Production;

use Yii;

class ProductionOrderFile extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_order_file';
    }

    public function rules()
    {
        return [
            [['order', 'stage'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['stage'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionStageOrder::className(), 'targetAttribute' => ['stage' => 'id']],
            [['order'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionOrder::className(), 'targetAttribute' => ['order' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => 'Order',
            'stage' => 'Stage',
            'name' => 'Name',
        ];
    }

    public function getNameStage()
    {
        return $this->hasOne(ProductionStageOrder::className(), ['id' => 'stage']);
    }

    public function getStatusStage()
    {
        return $this->hasOne(ProductionOrderPlanning::className(), ['order' => 'order', 'stage' => 'stage']);
    }
}
