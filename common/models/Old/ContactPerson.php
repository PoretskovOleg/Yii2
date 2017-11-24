<?php

namespace common\models\Old;

use Yii;

class ContactPerson extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'contact_person';
    }

}