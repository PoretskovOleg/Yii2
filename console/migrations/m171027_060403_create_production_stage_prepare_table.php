<?php

use yii\db\Migration;

class m171027_060403_create_production_stage_prepare_table extends Migration
{
    public function up()
    {
        $this->createTable('production_stage_prepare', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'shortName' => $this->string(),
        ]);

        $this->batchInsert('production_stage_prepare', ['name', 'shortName'], [
            ['Конструкторская документация', 'КД'],
            ['Материалы', 'М'],
            ['Тех карта', 'ТК'],
            ['Инструменты', 'И']
        ]);
    }

    public function down()
    {
        $this->dropTable('production_stage_prepare');
    }
}
