<?php

namespace common\helpers;


class UploadHelper
{
    public static function uploadWithUniqueFilename($file, $directory)
    {
        $filename = self::getUniqueFilename() . '.' . $file->extension;
        $path = \Yii::getAlias('@webroot') . $directory;

        if (\yii\helpers\FileHelper::createDirectory($path, $mode = 0775, $recursive = true)
            && $file->saveAs($path . '/' . $filename)
        ) {
            return $filename;
        }

        return false;
    }

    public static function getUniqueFilename()
    {
        return sha1(\Yii::$app->security->generateRandomString(10) . time());
    }
}