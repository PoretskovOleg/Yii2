<?php

namespace common\models\Production;

use Yii;

class ProductionStageOrder extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_stage_order';
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
}
