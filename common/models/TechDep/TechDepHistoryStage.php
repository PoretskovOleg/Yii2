<?php

namespace common\models\TechDep;

use Yii;
use common\models\User;

class TechDepHistoryStage extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_history_stage';
    }

    public function rules()
    {
        return [
            [['project', 'stage', 'status', 'createdAt', 'author'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepStatusStage::className(), 'targetAttribute' => ['status' => 'id']],
            [['project'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepProject::className(), 'targetAttribute' => ['project' => 'id']],
            [['stage'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepStagesProject::className(), 'targetAttribute' => ['stage' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project' => 'Project',
            'stage' => 'Stage',
            'status' => 'Status',
            'createdAt' => 'Created At',
            'author' => 'Author',
            'comment' => 'Comment',
        ];
    }

    public function getStatusStage()
    {
        return $this->hasOne(TechDepStatusStage::className(), ['id' => 'status']);
    }

    public function getAuthorHistory()
    {
        return $this->hasOne(User::className(), ['user_id' => 'author']);
    }
}
