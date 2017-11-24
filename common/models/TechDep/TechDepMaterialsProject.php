<?php

namespace common\models\TechDep;

use Yii;
use common\models\Old\Good;

class TechDepMaterialsProject extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_materials_project';
    }

    public function rules()
    {
        return [
            [['project', 'material'], 'integer'],
            [['quantity'], 'number'],
            [['project'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepProject::className(), 'targetAttribute' => ['project' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project' => 'Project',
            'material' => 'Material',
            'quantity' => 'Quantity',
        ];
    }

    public function getMaterialProject()
    {
        return $this->hasOne(Good::className(), ['goods_id' => 'material']);
    }
}
