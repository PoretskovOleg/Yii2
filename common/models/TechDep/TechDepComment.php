<?php

namespace common\models\TechDep;

use Yii;
use common\models\User;

class TechDepComment extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tech_dep_comment';
    }

    public function rules()
    {
        return [
            [['project', 'createdAt', 'author'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['project'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepProject::className(), 'targetAttribute' => ['project' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project' => 'Project',
            'createdAt' => 'Created At',
            'author' => 'Author',
            'comment' => 'Comment',
        ];
    }

    public function getAuthorComment()
    {
        return $this->hasOne(User::className(), ['user_id' => 'author']);
    }
}
