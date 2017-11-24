<?php

use yii\db\Migration;

/**
 * Handles dropping orderId from table `driver_trips`.
 */
class m170902_054755_drop_orderId_column_from_driver_trips_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn('driver_trips', 'orderId');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addColumn('driver_trips', 'orderId', $this->integer());
    }
}
