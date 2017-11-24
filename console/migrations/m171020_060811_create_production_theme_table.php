<?php

use yii\db\Migration;

class m171020_060811_create_production_theme_table extends Migration
{
    public function up()
    {
        $this->createTable('production_theme', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->batchInsert('production_theme', ['name'], [
            ['Сварка'],
            ['Мехобработка'],
            ['Гибка труб'],
            ['Плазменная резка']
        ]);
    }

    public function down()
    {
        $this->dropTable('production_theme');
    }
}
