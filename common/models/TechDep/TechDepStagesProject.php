<?php

namespace common\models\TechDep;

use Yii;

class TechDepStagesProject extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_stages_project';
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
}
