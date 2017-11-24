<?php

use yii\db\Migration;

class m170911_080634_create_tech_dep_priority_project_table extends Migration
{
    public function up()
    {
        $this->createTable('tech_dep_priority_project', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->batchInsert('tech_dep_priority_project', ['name'], [
            ['ОГОНЬ'],
            ['Важно'],
            ['Обычный']
        ]);
    }

    public function down()
    {
        $this->dropTable('tech_dep_priority_project');
    }
}
