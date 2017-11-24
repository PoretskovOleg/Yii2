<?php

use yii\db\Migration;

/**
 * Handles the creation of table `driver_car`.
 */
class m170807_104006_create_driver_car_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('driver_car', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'number' => $this->string(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('driver_car');
    }
}
