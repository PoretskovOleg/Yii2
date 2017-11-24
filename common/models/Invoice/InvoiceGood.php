<?php

namespace common\models\Invoice;


class InvoiceGood extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'invoice_goods';
    }

    public function rules()
    {
        return [
            [['invoice_id', 'name', 'quantity', 'end_price', 'index'], 'required'],
            [['invoice_id', 'good_id', 'quantity', 'unit_id', 'index', 'delivery_period'], 'integer'],
            [['price', 'mrc_percent', 'base_price_percent', 'discount', 'end_price', 'margin_percent', 'volume', 'weight'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№',
            'invoice_id' => 'Счёт',
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
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }
}
