<?php

use yii\db\Migration;

class m170814_125604_create_driver_status_trip_tickets_table extends Migration
{
    public function up()
    {
        $this->createTable('driver_status_trip_tickets', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'color' => $this->string(),
        ]);

        $this->batchInsert('driver_status_trip_tickets', ['name', 'color'], [
            ['Новый', 'green'],
            ['Утвержден', 'pink'],
            ['Ознакомлен', 'yellow'],
            ['Закрыт', 'grey'],
        ]);
    }

    public function down()
    {
        $this->dropTable('driver_status_trip_tickets');
    }
}
