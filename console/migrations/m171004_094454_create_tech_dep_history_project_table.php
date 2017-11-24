<?php

use yii\db\Migration;

class m171004_094454_create_tech_dep_history_project_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('tech_dep_history_project', [
            'id' => $this->primaryKey(),
            'project' => $this->integer(),
            'status' => $this->integer(),
            'createdAt' => $this->integer(),
            'author' => $this->integer(),
            'comment' => $this->string(),
        ]);

        // creates index for column `project`
        $this->createIndex(
            'idx-tech_dep_history_project-project',
            'tech_dep_history_project',
            'project'
        );

        // add foreign key for table `tech_dep_project`
        $this->addForeignKey(
            'fk-tech_dep_history_project-project',
            'tech_dep_history_project',
            'project',
            'tech_dep_project',
            'id',
            'CASCADE'
        );

        // creates index for column `status`
        $this->createIndex(
            'idx-tech_dep_history_project-status',
            'tech_dep_history_project',
            'status'
        );

        // add foreign key for table `tech_dep_status_project`
        $this->addForeignKey(
            'fk-tech_dep_history_project-status',
            'tech_dep_history_project',
            'status',
            'tech_dep_status_project',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `tech_dep_project`
        $this->dropForeignKey(
            'fk-tech_dep_history_project-project',
            'tech_dep_history_project'
        );

        // drops index for column `project`
        $this->dropIndex(
            'idx-tech_dep_history_project-project',
            'tech_dep_history_project'
        );

        // drops foreign key for table `tech_dep_status_project`
        $this->dropForeignKey(
            'fk-tech_dep_history_project-status',
            'tech_dep_history_project'
        );

        // drops index for column `status`
        $this->dropIndex(
            'idx-tech_dep_history_project-status',
            'tech_dep_history_project'
        );

        $this->dropTable('tech_dep_history_project');
    }
}
