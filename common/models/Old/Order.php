<?php

namespace common\models\Old;

use Yii;

class Order extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'orders';
    }

    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['organization_id' => 'organization_id']);
    }

    public function getPerformer()
    {
        return $this->hasOne(Organization::className(), ['organization_id' => 'performer_id']);
    }

    public function getCreateUser()
    {
        return $this->hasOne(\common\models\User::className(), ['user_id' => 'create_user_id'])
            ->select(['user.last_name', 'user.first_name', 'user.patronymic', 'user.phone_number']);
    }

    public function getContactPerson()
    {
        return $this->hasOne(ContactPerson::className(), ['contact_person_id' => 'contact_person_id']);
    }
}