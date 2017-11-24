<?php

namespace common\models\TechDep;

use Yii;

class TechDepDifficulty extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_difficulty';
    }

    public function rules()
    {
        return [
            [['difficulty', 'project', 'techTask', 'calc1', 'calc2', 'plan', 'calcTech', 'model', 'draw', 'spec', 'materials', 'tools', 'techMap', 'passport'], 'integer'],
            [['stageName'], 'string', 'max' => 15],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'difficulty' => 'Difficulty',
            'stageName' => 'Stage Name',
            'project' => 'Project',
            'techTask' => 'Tech Task',
            'calc1' => 'Calc1',
            'calc2' => 'Calc2',
            'plan' => 'Plan',
            'calcTech' => 'Calc Tech',
            'model' => 'Model',
            'draw' => 'Draw',
            'spec' => 'Spec',
            'materials' => 'Materials',
            'tools' => 'Tools',
            'techMap' => 'Tech Map',
        ];
    }
}
