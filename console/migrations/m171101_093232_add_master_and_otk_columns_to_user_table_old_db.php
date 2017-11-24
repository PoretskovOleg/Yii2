<?php

use yii\db\Migration;

class m171101_093232_add_master_and_otk_columns_to_user_table_old_db extends Migration
{
    public function safeUp()
    {
        Yii::$app->old_db->createCommand()->addColumn('user', 'master', 'integer null default null')->execute();
        Yii::$app->old_db->createCommand()->addColumn('user', 'otk', 'integer null default null')->execute();
    }

    public function safeDown()
    {
        Yii::$app->old_db->createCommand()->dropColumn('user', 'master')->execute();
        Yii::$app->old_db->createCommand()->dropColumn('user', 'otk')->execute();
    }
}
