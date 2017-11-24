<?php

use yii\db\Migration;

class m171004_064400_create_tech_dep_history_stage_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('tech_dep_history_stage', [
            'id' => $this->primaryKey(),
            'project' => $this->integer(),
            'stage' => $this->integer(),
            'status' => $this->integer(),
            'createdAt' => $this->integer(),
            'author' => $this->integer(),
            'comment' => $this->string(),
        ]);

        // creates index for column `project`
        $this->createIndex(
            'idx-tech_dep_history_stage-project',
            'tech_dep_history_stage',
            'project'
        );

        // add foreign key for table `tech_dep_project`
        $this->addForeignKey(
            'fk-tech_dep_history_stage-project',
            'tech_dep_history_stage',
            'project',
            'tech_dep_project',
            'id',
            'CASCADE'
        );

        // creates index for column `stage`
        $this->createIndex(
            'idx-tech_dep_history_stage-stage',
            'tech_dep_history_stage',
            'stage'
        );

        // add foreign key for table `tech_dep_stages_project`
        $this->addForeignKey(
            'fk-tech_dep_history_stage-stage',
            'tech_dep_history_stage',
            'stage',
            'tech_dep_stages_project',
            'id',
            'CASCADE'
        );

        // creates index for column `status`
        $this->createIndex(
            'idx-tech_dep_history_stage-status',
            'tech_dep_history_stage',
            'status'
        );

        // add foreign key for table `tech_dep_status_stage`
        $this->addForeignKey(
            'fk-tech_dep_history_stage-status',
            'tech_dep_history_stage',
            'status',
            'tech_dep_status_stage',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `tech_dep_project`
        $this->dropForeignKey(
            'fk-tech_dep_history_stage-project',
            'tech_dep_history_stage'
        );

        // drops index for column `project`
        $this->dropIndex(
            'idx-tech_dep_history_stage-project',
            'tech_dep_history_stage'
        );

        // drops foreign key for table `tech_dep_stages_project`
        $this->dropForeignKey(
            'fk-tech_dep_history_stage-stage',
            'tech_dep_history_stage'
        );

        // drops index for column `stage`
        $this->dropIndex(
            'idx-tech_dep_history_stage-stage',
            'tech_dep_history_stage'
        );

        // drops foreign key for table `tech_dep_status_stage`
        $this->dropForeignKey(
            'fk-tech_dep_history_stage-status',
            'tech_dep_history_stage'
        );

        // drops index for column `status`
        $this->dropIndex(
            'idx-tech_dep_history_stage-status',
            'tech_dep_history_stage'
        );

        $this->dropTable('tech_dep_history_stage');
    }
}
