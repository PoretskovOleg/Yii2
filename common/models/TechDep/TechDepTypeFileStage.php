<?php

namespace common\models\TechDep;

use Yii;

class TechDepTypeFileStage extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_type_file_stage';
    }

    public function rules()
    {
        return [
            [['stage'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['stage'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepStagesProject::className(), 'targetAttribute' => ['stage' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'stage' => 'Stage',
        ];
    }

    public function getStageProject()
    {
        return $this->hasOne(TechDepStagesProject::className(), ['id' => 'stage']);
    }
}
