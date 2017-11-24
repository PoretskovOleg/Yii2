<?php

use yii\db\Migration;

class m170918_050813_create_tech_dep_status_stage_table extends Migration
{
    public function up()
    {
        $this->createTable('tech_dep_status_stage', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'color' => $this->string(),
        ]);

        $this->batchInsert('tech_dep_status_stage', ['name', 'color'], [
            ['В ожидании', 'purple'],
            ['В работе', 'teal'],
            ['На утверждении', 'blue'],
            ['Утвержден', 'grey']
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('tech_dep_status_stage');
    }
}
