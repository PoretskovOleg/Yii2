<?php

use yii\db\Migration;

/**
 * Handles the creation of table `driver_status_trips`.
 */
class m170804_064859_create_driver_status_trips_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('driver_status_trips', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'color' => $this->string(),
        ]);

        $this->batchInsert('driver_status_trips', ['name', 'color'], [
            ['Новая', 'green'],
            ['Подготовлена', 'blue'],
            ['В плане', 'yellow'],
            ['Можно везти', 'pink'],
            ['В путевом листе', 'blue'],
            ['Доставлена', 'grey'],
            ['Отменена', 'grey']
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('driver_status_trips');
    }
}
