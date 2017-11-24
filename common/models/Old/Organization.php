<?php

namespace common\models\Old;

use Yii;

class Organization extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'organization';
    }

    public function getStampFilename()
    {
        $file = File::findOne(['fid_id' => $this->stamp_fid]);
        if ($file) {
            return $file->path;
        }
        return false;
    }
}