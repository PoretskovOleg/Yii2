<?php

namespace common\models\TechDep;

use Yii;

class TechDepStageFile extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_stage_file';
    }

    public function rules()
    {
        return [
            [['project', 'stage', 'type'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['stage'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepStagesProject::className(), 'targetAttribute' => ['stage' => 'id']],
            [['project'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepProject::className(), 'targetAttribute' => ['project' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepTypeFileStage::className(), 'targetAttribute' => ['type' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project' => 'Project',
            'stage' => 'Stage',
            'name' => 'Name',
        ];
    }

    public function getStageProject()
    {
        return $this->hasOne(TechDepStagesProject::className(), ['id' => 'stage']);
    }

    public function getTypeFile()
    {
        return $this->hasOne(TechDepTypeFileStage::className(), ['id' => 'type']);
    }

    public function getProject0()
    {
        return $this->hasOne(TechDepProject::className(), ['id' => 'project']);
    }
}
