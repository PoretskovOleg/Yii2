<?php

use yii\db\Migration;

class m170925_070726_create_tech_dep_stage_file_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('tech_dep_stage_file', [
            'id' => $this->primaryKey(),
            'project' => $this->integer(),
            'stage' => $this->integer(),
            'name' => $this->string(),
        ]);

        // creates index for column `project`
        $this->createIndex(
            'idx-tech_dep_stage_file-project',
            'tech_dep_stage_file',
            'project'
        );

        // add foreign key for table `tech_dep_project`
        $this->addForeignKey(
            'fk-tech_dep_stage_file-project',
            'tech_dep_stage_file',
            'project',
            'tech_dep_project',
            'id',
            'CASCADE'
        );

        // creates index for column `stage`
        $this->createIndex(
            'idx-tech_dep_stage_file-stage',
            'tech_dep_stage_file',
            'stage'
        );

        // add foreign key for table `tech_dep_stages_project`
        $this->addForeignKey(
            'fk-tech_dep_stage_file-stage',
            'tech_dep_stage_file',
            'stage',
            'tech_dep_stages_project',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `tech_dep_project`
        $this->dropForeignKey(
            'fk-tech_dep_stage_file-project',
            'tech_dep_stage_file'
        );

        // drops index for column `project`
        $this->dropIndex(
            'idx-tech_dep_stage_file-project',
            'tech_dep_stage_file'
        );

        // drops foreign key for table `tech_dep_stages_project`
        $this->dropForeignKey(
            'fk-tech_dep_stage_file-stage',
            'tech_dep_stage_file'
        );

        // drops index for column `stage`
        $this->dropIndex(
            'idx-tech_dep_stage_file-stage',
            'tech_dep_stage_file'
        );

        $this->dropTable('tech_dep_stage_file');
    }
}
