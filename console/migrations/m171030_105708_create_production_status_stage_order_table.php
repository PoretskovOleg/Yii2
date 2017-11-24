<?php

use yii\db\Migration;

class m171030_105708_create_production_status_stage_order_table extends Migration
{
    public function up()
    {
        $this->createTable('production_status_stage_order', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->batchInsert('production_status_stage_order', ['name'], [
            ['Не активен'],
            ['В работе'],
            ['Завершен']
        ]);
    }

    public function down()
    {
        $this->dropTable('production_status_stage_order');
    }
}
