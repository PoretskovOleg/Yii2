<?php

namespace common\models\TechDep;

use Yii;

class TechDepStatusProject extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_status_project';
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

    public function getTechDepProjects()
    {
        return $this->hasMany(TechDepProject::className(), ['status' => 'id']);
    }
}
