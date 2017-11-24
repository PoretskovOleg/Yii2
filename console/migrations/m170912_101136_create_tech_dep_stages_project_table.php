<?php

use yii\db\Migration;

class m170912_101136_create_tech_dep_stages_project_table extends Migration
{
    public function up()
    {
        $this->createTable('tech_dep_stages_project', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'shortName' => $this->string(),
        ]);

        $this->batchInsert('tech_dep_stages_project', ['name', 'shortName'], [
            ['Расчет Д1', 'Расчет Д1'],
            ['Расчет Д2', 'Расчет Д2'],
            ['Расчет тех', 'Расчет тех'],
            ['Справка на материалы', 'Спр на матер'],
            ['Модель', 'Модель'],
            ['Чертеж', 'Чертеж'],
            ['Спецификация', 'Спецификация'],
            ['Справка на инструмент', 'Спр на инстр'],
            ['Тех. карта', 'Тех.карта'],
            ['Паспорт, инструкция', 'Паспорт, инстр']
        ]);
    }

    public function down()
    {
        $this->dropTable('tech_dep_stages_project');
    }
}
