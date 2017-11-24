<?php

namespace common\models\Old;

use Yii;

class Signer extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'signatory';
    }

    public function getSignFilename()
    {
        $file = File::findOne(['fid_id' => $this->sign_img_fid]);
        if ($file) {
            return $file->path;
        }
        return false;
    }
}