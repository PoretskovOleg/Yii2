<?php

use yii\db\Migration;

class m170802_124753_add_auth_key_column_to_user_table_old_db extends Migration
{
    public function up()
    {
        Yii::$app->old_db->createCommand()->addColumn('user', 'auth_key', 'varchar(32) null default null')->execute();
    }

    public function down()
    {
        Yii::$app->old_db->createCommand()->dropColumn('user', 'auth_key')->execute();
    }
}
