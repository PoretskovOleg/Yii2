<?php

namespace common\models\TechDep;

use Yii;
use common\models\User;

class TechDepHistoryProject extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_history_project';
    }

    public function rules()
    {
        return [
            [['project', 'status', 'createdAt', 'author'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepStatusProject::className(), 'targetAttribute' => ['status' => 'id']],
            [['project'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepProject::className(), 'targetAttribute' => ['project' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project' => 'Project',
            'status' => 'Status',
            'createdAt' => 'Created At',
            'author' => 'Author',
            'comment' => 'Comment',
        ];
    }

    public function getStatusProject()
    {
        return $this->hasOne(TechDepStatusProject::className(), ['id' => 'status']);
    }

    public function getAuthorHistory()
    {
        return $this->hasOne(User::className(), ['user_id' => 'author']);
    }
}
