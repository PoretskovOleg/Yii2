<?php

use yii\db\Migration;

/**
 * Handles the creation of table `driver_type_trip`.
 */
class m170802_125432_create_driver_type_trip_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('driver_type_trip', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'sign' => $this->string(),
        ]);

        $this->batchInsert('driver_type_trip', ['name', 'sign'], [
            ['Доставка', 'car.png'],
            ['Закупка', 'buy.png'],
            ['Перемещение', 'doublearrow.png'],
            ['Поручение', 'star.png']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('driver_type_trip');
    }
}
