<?php

namespace common\models\CommercialProposal;

use Yii;


class CommercialProposalStatus extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'commercial_proposal_statuses';
    }
}
