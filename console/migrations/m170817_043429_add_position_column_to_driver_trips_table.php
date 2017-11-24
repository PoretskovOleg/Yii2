<?php

use yii\db\Migration;

/**
 * Handles adding position to table `driver_trips`.
 */
class m170817_043429_add_position_column_to_driver_trips_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('driver_trips', 'position', $this->smallInteger());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('driver_trips', 'position');
    }
}
