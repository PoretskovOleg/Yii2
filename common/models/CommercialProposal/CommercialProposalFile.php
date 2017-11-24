<?php

namespace common\models\CommercialProposal;

use Yii;


class CommercialProposalFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'commercial_proposal_files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['commercial_proposal_id', 'filename'], 'required'],
            [['commercial_proposal_id'], 'integer'],
            [['filename'], 'string', 'max' => 255],
            [['commercial_proposal_id'], 'exist', 'skipOnError' => true, 'targetClass' => CommercialProposal::className(), 'targetAttribute' => ['commercial_proposal_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'commercial_proposal_id' => 'Commercial Proposal ID',
            'filename' => 'Filename',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommercialProposal()
    {
        return $this->hasOne(CommercialProposals::className(), ['id' => 'commercial_proposal_id']);
    }
}
