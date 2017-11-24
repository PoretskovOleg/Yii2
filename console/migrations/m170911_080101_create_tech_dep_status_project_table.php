<?php

use yii\db\Migration;

class m170911_080101_create_tech_dep_status_project_table extends Migration
{
    public function up()
    {
        $this->createTable('tech_dep_status_project', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'color' => $this->string()
        ]);

        $this->batchInsert('tech_dep_status_project', ['name', 'color'], [
            ['ТЗ', 'green'],
            ['Расчет Д1', 'pink'],
            ['Расчет Д2', 'orange'],
            ['У Менеджера', 'brown'],
            ['Планирование', 'yellow'],
            ['Готов к работе', 'purple'],
            ['В работе', 'teal'],
            ['На утверждении', 'blue'],
            ['Утвержден', 'grey']
        ]);
    }

    public function down()
    {
        $this->dropTable('tech_dep_status_project');
    }
}
