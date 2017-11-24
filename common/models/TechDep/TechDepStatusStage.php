<?php

namespace common\models\TechDep;

use Yii;

class TechDepStatusStage extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_status_stage';
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
            'color' => 'Color'
        ];
    }

    public function getTechDepPlannings()
    {
        return $this->hasMany(TechDepPlanning::className(), ['status' => 'id']);
    }
}
