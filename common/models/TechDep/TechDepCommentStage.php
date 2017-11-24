<?php

namespace common\models\TechDep;

use Yii;
use common\models\User;

class TechDepCommentStage extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_comment_stage';
    }

    public function rules()
    {
        return [
            [['project', 'stage', 'author', 'createdAt'], 'integer'],
            [['comment'], 'string'],
            [['stage'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepStagesProject::className(), 'targetAttribute' => ['stage' => 'id']],
            [['project'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepProject::className(), 'targetAttribute' => ['project' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project' => 'Project',
            'stage' => 'Stage',
            'author' => 'Author',
            'createdAt' => 'Created At',
            'comment' => 'Comment',
        ];
    }

    public function getAuthorComment()
    {
        return $this->hasOne(User::className(), ['user_id' => 'author']);
    }
}
