<?php

namespace common\models\Production;

use Yii;
use common\models\User;

class ProductionOrderComment extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'production_order_comment';
    }

    public function rules()
    {
        return [
            [['order', 'createdAt', 'author'], 'integer'],
            [['comment'], 'string'],
            [['order'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionOrder::className(), 'targetAttribute' => ['order' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order' => 'Order',
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
