<?php

use yii\db\Migration;

class m170821_053119_add_region_column_to_driver_address_table extends Migration
{
    public function up()
    {
        $this->addColumn('driver_address', 'region', $this->smallInteger());
    }

    public function down()
    {
        $this->dropColumn('driver_address', 'region');
    }
}
