<?php

namespace common\models\Invoice;


class InvoiceStatus extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'invoice_statuses';
    }
}
