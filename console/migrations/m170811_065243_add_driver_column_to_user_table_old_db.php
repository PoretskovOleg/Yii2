<?php

use yii\db\Migration;

class m170811_065243_add_driver_column_to_user_table_old_db extends Migration
{
    public function up()
    {
        Yii::$app->old_db->createCommand()->addColumn('user', 'driver', 'integer null default null')->execute();
    }

    public function down()
    {
        Yii::$app->old_db->createCommand()->dropColumn('user', 'driver')->execute();
    }
}
