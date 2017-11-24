<?php

use yii\db\Migration;

class m170824_044523_add_time_unload_column_to_driver_trips_table extends Migration
{
    public function up()
    {
        $this->addColumn('driver_trips', 'timeUnload', $this->smallInteger());
    }

    public function down()
    {
        $this->dropColumn('driver_trips', 'timeUnload');
    }
}
