<?php

namespace common\models\Old;

use Yii;

class Contractor extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'contractor';
    }

    public function getOrganizations() {
        return $this->hasMany(Organization::className(), ['contractor_id' => 'contractor_id']);
    }

    public function getContact_persons() {
        return $this->hasMany(ContactPerson::className(), ['contractor_id' => 'contractor_id']);
    }

    public function getType0() {
        return $this->hasOne(ContractorType::className(), ['list_contractor_type_id' => 'type']);
    }

    public function getManager() {
        return $this->hasOne(\common\models\User::className(), ['user_id' => 'user_id']);
    }
}