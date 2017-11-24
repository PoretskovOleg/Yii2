<?php

namespace common\models\Production;

use Yii;

class ProductionPrepareOrder extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_prepare_order';
    }

    public function rules()
    {
        return [
            [['order', 'stage', 'isPrepare'], 'integer'],
            [['stage'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionStagePrepare::className(), 'targetAttribute' => ['stage' => 'id']],
            [['order'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionOrder::className(), 'targetAttribute' => ['order' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => 'Order',
            'stage' => 'Stage',
            'isPrepare' => 'Is Prepare',
        ];
    }

    public function getStagePrepare()
    {
        return $this->hasOne(ProductionStagePrepare::className(), ['id' => 'stage']);
    }

}
