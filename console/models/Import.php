<?php

namespace console\models;

use yii\base\Model;

class Import extends Model
{
    public static function getAll($table)
    {
        return (new \yii\db\Query())
            ->select('*')
            ->from($table)
            ->all(\Yii::$app->old_db);
    }

    public static function batchInsert($table, $fields, $rows)
    {
        \Yii::$app->db->createCommand()
            ->batchInsert($table, $fields, $rows)
            ->execute();
    }
}
