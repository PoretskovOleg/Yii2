<?php

use yii\db\Migration;

class m170914_071735_create_tech_dep_file_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('tech_dep_file', [
            'id' => $this->primaryKey(),
            'project' => $this->integer(),
            'name' => $this->string(),
        ]);

        // creates index for column `project`
        $this->createIndex(
            'idx-tech_dep_file-project',
            'tech_dep_file',
            'project'
        );

        // add foreign key for table `tech_dep_project`
        $this->addForeignKey(
            'fk-tech_dep_file-project',
            'tech_dep_file',
            'project',
            'tech_dep_project',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `tech_dep_project`
        $this->dropForeignKey(
            'fk-tech_dep_file-project',
            'tech_dep_file'
        );

        // drops index for column `project`
        $this->dropIndex(
            'idx-tech_dep_file-project',
            'tech_dep_file'
        );

        $this->dropTable('tech_dep_file');
    }
}
