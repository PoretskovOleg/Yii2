<?php

use yii\db\Migration;

class m171020_061405_create_production_status_order_table extends Migration
{
    public function up()
    {
        $this->createTable('production_status_order', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'color' => $this->string(),
        ]);

        $this->batchInsert('production_status_order', ['name', 'color'], [
            ['Новый', 'green'],
            ['Согласован', 'yellow'],
            ['Можно делать', 'pink'],
            ['В работе', 'blue'],
            ['Приостановлен', 'purple'],
            ['Отменен', 'brown'],
            ['Завершен', 'grey']
        ]);
    }

    public function down()
    {
        $this->dropTable('production_status_order');
    }
}
