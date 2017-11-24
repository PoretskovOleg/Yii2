<?php

use yii\db\Migration;

/**
 * Handles the creation of table `driver_address`.
 */
class m170802_121340_create_driver_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('driver_address', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'address' => $this->string(),
            'from' => $this->smallInteger(1),
            'to' => $this->smallInteger(1),
            'tk' => $this->smallInteger(1),
        ]);

        $this->insert('driver_address', [
            'name' => 'Другое',
            'address' => null,
            'from' => 1,
            'to' => 1,
            'tk' => 0
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('driver_address');
    }
}
