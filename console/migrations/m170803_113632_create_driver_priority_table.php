<?php

use yii\db\Migration;

/**
 * Handles the creation of table `driver_priority`.
 */
class m170803_113632_create_driver_priority_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('driver_priority', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->batchInsert('driver_priority', ['name'], [
            ['ОГОНЬ'],
            ['Важно'],
            ['Обычный']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('driver_priority');
    }
}
