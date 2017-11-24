<?php

namespace common\models\TechDep;

use Yii;

class TechDepFile extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_file';
    }

    public function rules()
    {
        return [
            [['project'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['project'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepProject::className(), 'targetAttribute' => ['project' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project' => 'Project',
            'name' => 'Name',
        ];
    }

    public function getProject0()
    {
        return $this->hasOne(TechDepProject::className(), ['id' => 'project']);
    }
}
