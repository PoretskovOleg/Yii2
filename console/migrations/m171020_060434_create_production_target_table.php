<?php

use yii\db\Migration;

class m171020_060434_create_production_target_table extends Migration
{
    public function up()
    {
        $this->createTable('production_target', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->batchInsert('production_target', ['name'], [
            ['Заказ'],
            ['Склад'],
            ['Другое']
        ]);
    }

    public function down()
    {
        $this->dropTable('production_target');
    }
}
