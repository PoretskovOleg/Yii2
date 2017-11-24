<?php

namespace common\models\TechDep;

use Yii;

class TechDepPriorityProject extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_priority_project';
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

    public function getTechDepProjects()
    {
        return $this->hasMany(TechDepProject::className(), ['priority' => 'id']);
    }
}
