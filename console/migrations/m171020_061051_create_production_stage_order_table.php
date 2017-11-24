<?php

use yii\db\Migration;

class m171020_061051_create_production_stage_order_table extends Migration
{
    public function up()
    {
        $this->createTable('production_stage_order', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'color' => $this->string(),
        ]);

        $this->batchInsert('production_stage_order', ['name', 'color'], [
            ['Заготовка', 'green'],
            ['Деталировка', 'yellow'],
            ['Сварка', 'pink'],
            ['Зачистка', 'blue'],
            ['Покраска', 'orange'],
            ['Сушка', 'brown'],
            ['ОТК', 'purple'],
            ['На склад', 'teal']
        ]);
    }

    public function down()
    {
        $this->dropTable('production_stage_order');
    }
}
