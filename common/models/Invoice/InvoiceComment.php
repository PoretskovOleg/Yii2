<?php

namespace common\models\Invoice;

use Yii;
use common\models\User;


class InvoiceComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_id'], 'required'],
            [['invoice_id', 'status_id', 'user_id'], 'integer'],
            [['text'], 'string'],
            [['deadline', 'created'], 'safe'],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => InvoiceStatus::className(), 'targetAttribute' => ['status_id' => 'id']],
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
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(InvoiceStatus::className(), ['id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }
}
