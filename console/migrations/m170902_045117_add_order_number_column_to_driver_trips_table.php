<?php

use yii\db\Migration;

class m170902_045117_add_order_number_column_to_driver_trips_table extends Migration
{
    public function up()
    {
        $this->addColumn('driver_trips', 'orderNumber', $this->string(10));
    }

    public function down()
    {
        $this->dropColumn('driver_trips', 'orderNumber');
    }
}
