<?php

use yii\db\Migration;

class m170918_050814_create_tech_dep_planning_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('tech_dep_planning', [
            'id' => $this->primaryKey(),
            'project' => $this->integer(),
            'stage' => $this->integer(),
            'status' => $this->integer(),
            'dedlineTime' => $this->smallInteger(),
            'pureTime' => $this->smallInteger(),
            'contractor' => $this->integer(),
        ]);

        // creates index for column `project`
        $this->createIndex(
            'idx-tech_dep_planning-project',
            'tech_dep_planning',
            'project'
        );

        // add foreign key for table `tech_dep_project`
        $this->addForeignKey(
            'fk-tech_dep_planning-project',
            'tech_dep_planning',
            'project',
            'tech_dep_project',
            'id',
            'CASCADE'
        );

        // creates index for column `stage`
        $this->createIndex(
            'idx-tech_dep_planning-stage',
            'tech_dep_planning',
            'stage'
        );

        // add foreign key for table `tech_dep_stages_project`
        $this->addForeignKey(
            'fk-tech_dep_planning-stage',
            'tech_dep_planning',
            'stage',
            'tech_dep_stages_project',
            'id',
            'CASCADE'
        );

        // creates index for column `status`
        $this->createIndex(
            'idx-tech_dep_planning-status',
            'tech_dep_planning',
            'status'
        );

        // add foreign key for table `tech_dep_stages_project`
        $this->addForeignKey(
            'fk-tech_dep_planning-status',
            'tech_dep_planning',
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
            'fk-tech_dep_planning-project',
            'tech_dep_planning'
        );

        // drops index for column `project`
        $this->dropIndex(
            'idx-tech_dep_planning-project',
            'tech_dep_planning'
        );

        // drops foreign key for table `tech_dep_stages_project`
        $this->dropForeignKey(
            'fk-tech_dep_planning-stage',
            'tech_dep_planning'
        );

        // drops index for column `stage`
        $this->dropIndex(
            'idx-tech_dep_planning-stage',
            'tech_dep_planning'
        );

        // drops foreign key for table `tech_dep_status_stage`
        $this->dropForeignKey(
            'fk-tech_dep_planning-status',
            'tech_dep_planning'
        );

        // drops index for column `status`
        $this->dropIndex(
            'idx-tech_dep_planning-status',
            'tech_dep_planning'
        );

        $this->dropTable('tech_dep_planning');
    }
}
