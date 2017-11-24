<?php

use yii\db\Migration;

class m171020_060247_create_production_priority_table extends Migration
{
    public function up()
    {
        $this->createTable('production_priority', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->batchInsert('production_priority', ['name'], [
            ['ОГОНЬ'],
            ['Важно'],
            ['Обычный']
        ]);
    }

    public function down()
    {
        $this->dropTable('production_priority');
    }
}
