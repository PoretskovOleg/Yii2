<?php

namespace common\models\Old;

use Yii;

class File extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'file';
    }
}