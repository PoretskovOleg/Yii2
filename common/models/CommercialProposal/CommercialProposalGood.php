<?php

namespace common\models\CommercialProposal;

use Yii;


class CommercialProposalGood extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'commercial_proposal_goods';
    }

    public function rules()
    {
        return [
            [['commercial_proposal_id', 'name', 'quantity', 'end_price', 'index'], 'required'],
            [['commercial_proposal_id', 'good_id', 'quantity', 'unit_id', 'index', 'delivery_period'], 'integer'],
            [['price', 'mrc_percent', 'base_price_percent', 'discount', 'end_price', 'margin_percent', 'volume', 'weight'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['commercial_proposal_id'], 'exist', 'skipOnError' => true, 'targetClass' => CommercialProposal::className(), 'targetAttribute' => ['commercial_proposal_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№',
            'commercial_proposal_id' => 'Коммерческое предложение',
            'good_id' => 'Связанный товар',
            'name' => 'Наименование',
            'quantity' => 'Кол-во',
            'unit_id' => 'Ед. изм',
            'price' => 'Себестоимость',
            'mrc_percent' => 'МРЦ',
            'base_price_percent' => 'Базовая цена',
            'discount' => 'Скидка',
            'end_price' => 'Окончательная цена',
            'margin_percent' => 'Маржа',
        ];
    }

    public function getCommercialProposal()
    {
        return $this->hasOne(CommercialProposal::className(), ['id' => 'commercial_proposal_id']);
    }
}
