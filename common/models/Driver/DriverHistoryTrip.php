<?php

namespace common\models\Driver;

use Yii;
use common\models\User;

class DriverHistoryTrip extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'driver_history_trip';
    }

    public function rules()
    {
        return [
            [['comment'], 'required'],
            [['trip', 'status', 'createdAt', 'author'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['trip'], 'exist', 'skipOnError' => true, 'targetClass' => DriverTrip::className(), 'targetAttribute' => ['trip' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => DriverStatusTrip::className(), 'targetAttribute' => ['status' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trip' => 'Trip',
            'status' => 'Status',
            'createdAt' => 'Created At',
            'author' => 'Author',
            'comment' => 'Комментарий',
        ];
    }

    public function getTripInfo()
    {
        return $this->hasOne(DriverTrip::className(), ['id' => 'trip']);
    }

    public function getStatusTrip()
    {
        return $this->hasOne(DriverStatusTrip::className(), ['id' => 'status']);
    }

    public function getAuthorHistory()
    {
        return $this->hasOne(User::className(), ['user_id' => 'author'])
            ->select(['user.last_name', 'user.first_name', 'user.patronymic']);
    }
}
