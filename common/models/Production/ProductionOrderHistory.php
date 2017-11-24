<?php

namespace common\models\Production;

use Yii;
use common\models\User;

class ProductionOrderHistory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_order_history';
    }

    public function rules()
    {
        return [
            [['order', 'status', 'createdAt', 'author'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionStatusOrder::className(), 'targetAttribute' => ['status' => 'id']],
            [['order'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionOrder::className(), 'targetAttribute' => ['order' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => 'Order',
            'status' => 'Status',
            'createdAt' => 'Created At',
            'author' => 'Author',
            'comment' => 'Comment',
        ];
    }

    public function getStatusOrder()
    {
        return $this->hasOne(ProductionStatusOrder::className(), ['id' => 'status']);
    }

    public function getAuthorHistory()
    {
        return $this->hasOne(User::className(), ['user_id' => 'author']);
    }
}
