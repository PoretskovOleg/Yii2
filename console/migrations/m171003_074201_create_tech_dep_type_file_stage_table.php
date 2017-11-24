<?php

use yii\db\Migration;

class m171003_074201_create_tech_dep_type_file_stage_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('tech_dep_type_file_stage', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'stage' => $this->integer(),
            'hint' => $this->string(),
            'countFiles' => $this->smallInteger()
        ]);

        // creates index for column `stage`
        $this->createIndex(
            'idx-tech_dep_type_file_stage-stage',
            'tech_dep_type_file_stage',
            'stage'
        );

        // add foreign key for table `tech_dep_stages_project`
        $this->addForeignKey(
            'fk-tech_dep_type_file_stage-stage',
            'tech_dep_type_file_stage',
            'stage',
            'tech_dep_stages_project',
            'id',
            'CASCADE'
        );

        $this->batchInsert('tech_dep_type_file_stage', ['name', 'stage', 'hint', 'countFiles'], [
            ['Расчет Д1', 1, 'Загрузите файл Расчета Д1', 1],
            ['Расчет Д2', 2, 'Загрузите файл Расчета Д2', 1],
            ['Расчет технический', 3, 'Загрузите файлы технического расчета', 1],
            ['Архив файлов модели', 5, 'Загрузите всю Вашу модель', 1],
            ['Модель в формате стэп', 5, 'Загрузите модель сохраненную в формате STP', 1],
            ['Картинки рендеринга', 5, 'Загрузите картинки рендеринга изделия в разных ракурсах, увеличенные участки важных элементов, от 3 шт – для сайта', 3],
            ['Габаритный чертеж', 5, 'Загрузите сброчный чертеж с основными габаритными и присоединительными размерами, с основными характеристиками – для сайта и согласования с клиентом', 1],
            ['Файлы чертежей в исходниках', 6, 'Загрузите файлы чертежей в автокад', 1],
            ['Комплект чертежей в PDF', 6, 'Загрузите комплект чертежей в PDF', 1],
            ['Спецификация в исходниках', 7, 'Загрузите файлы спецификации', 1],
            ['Спецификация в PDF', 7, 'Загрузите спецификацию в PDF', 1],
            ['Справка на материалы в EXCEL', 4, 'Загрузите файлы справки на материалы', 1],
            ['Справка на материалы в PDF', 4, 'Загрузите справку на материалы в PDF', 1],
            ['Справка на инструмент в EXCEL', 8, 'Загрузите файлы справки на инструмент', 1],
            ['Справка на инструмент в PDF', 8, 'Загрузите справку на инструмент в PDF', 1],
            ['Техкарта в исходнике', 9, 'Загрузите файлы техкарты', 1],
            ['Техкарта в PDF', 9, 'Загрузите техкарту в PDF', 1],
            ['Паспорт, инструкция, схемы в WORD', 10, 'Загрузите файлы паспорта и инструкции', 1],
            ['Паспорт, инструкция, схемы в PDF', 10, 'Загрузите паспорт и инструкции в PDF', 1]
        ]);
    }

    public function safeDown()
    {
        // drops foreign key for table `tech_dep_stages_project`
        $this->dropForeignKey(
            'fk-tech_dep_type_file_stage-stage',
            'tech_dep_type_file_stage'
        );

        // drops index for column `stage`
        $this->dropIndex(
            'idx-tech_dep_type_file_stage-stage',
            'tech_dep_type_file_stage'
        );

        $this->dropTable('tech_dep_type_file_stage');
    }
}
