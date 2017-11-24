<?php

namespace common\models\Old;

use Yii;

class Position extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'post';
    }
}
