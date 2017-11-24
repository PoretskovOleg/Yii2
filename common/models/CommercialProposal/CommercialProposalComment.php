<?php

namespace common\models\CommercialProposal;

use Yii;
use common\models\User;

/**
 * This is the model class for table "commercial_proposal_comments".
 *
 * @property integer $id
 * @property integer $commercial_proposal_id
 * @property integer $status_id
 * @property integer $user_id
 * @property string $text
 * @property string $deadline
 * @property string $created
 *
 * @property CommercialProposal $commercialProposal
 * @property CommercialProposalStatus $status
 * @property User $user
 */
class CommercialProposalComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'commercial_proposal_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['commercial_proposal_id'], 'required'],
            [['commercial_proposal_id', 'status_id', 'user_id'], 'integer'],
            [['text'], 'string'],
            [['deadline', 'created'], 'safe'],
            [['commercial_proposal_id'], 'exist', 'skipOnError' => true, 'targetClass' => CommercialProposal::className(), 'targetAttribute' => ['commercial_proposal_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => CommercialProposalStatus::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№',
            'status_id' => 'Статус',
            'user_id' => 'Автор',
            'text' => 'Комментарий',
            'deadline' => 'Дедлайн',
        ];
    }

    public function beforeSave($insert) {
        $this->user_id = Yii::$app->user->getId();

        if (empty($this->created)) {
            $timezone = new \DateTimeZone('Europe/Moscow');
            $this->created = (new \DateTime('now', $timezone))->format('Y-m-d H:i:s');
        }

        if (!empty($this->deadline)) {
            $timezone = new \DateTimeZone('Europe/Moscow');
            $this->deadline = (new \DateTime($this->deadline, $timezone))->format('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommercialProposal()
    {
        return $this->hasOne(CommercialProposal::className(), ['id' => 'commercial_proposal_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(CommercialProposalStatus::className(), ['id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }
}
