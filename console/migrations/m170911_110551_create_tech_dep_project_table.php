<?php

use yii\db\Migration;

class m170911_110551_create_tech_dep_project_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('tech_dep_project', [
            'id' => $this->primaryKey(),
            'createdAt' => $this->integer(),
            'timeStart' => $this->integer(),
            'readyWork' => $this->integer(),
            'inWork' => $this->integer(),
            'dedline' => $this->integer(),
            'authorId' => $this->integer(),
            'orderNumber' => $this->string(),
            'goodId' => $this->integer(),
            'goodName' => $this->string(),
            'type' => $this->integer(),
            'status' => $this->integer(),
            'priority' => $this->integer(),
            'difficulty' => $this->integer(),
            'responsible' => $this->integer(),
            'approved' => $this->integer(),
            'notice' => $this->string(),
            'changes' => $this->string()
        ]);

        // creates index for column `typeProject`
        $this->createIndex(
            'idx-tech_dep_project-type',
            'tech_dep_project',
            'type'
        );

        // add foreign key for table `tech_dep_type_project`
        $this->addForeignKey(
            'fk-tech_dep_project-type',
            'tech_dep_project',
            'type',
            'tech_dep_type_project',
            'id',
            'CASCADE'
        );

        // creates index for column `status`
        $this->createIndex(
            'idx-tech_dep_project-status',
            'tech_dep_project',
            'status'
        );

        // add foreign key for table `tech_dep_status_project`
        $this->addForeignKey(
            'fk-tech_dep_project-status',
            'tech_dep_project',
            'status',
            'tech_dep_status_project',
            'id',
            'CASCADE'
        );

        // creates index for column `priority`
        $this->createIndex(
            'idx-tech_dep_project-priority',
            'tech_dep_project',
            'priority'
        );

        // add foreign key for table `tech_dep_priority_project`
        $this->addForeignKey(
            'fk-tech_dep_project-priority',
            'tech_dep_project',
            'priority',
            'tech_dep_priority_project',
            'id',
            'CASCADE'
        );

        // creates index for column `difficulty`
        $this->createIndex(
            'idx-tech_dep_project-difficulty',
            'tech_dep_project',
            'difficulty'
        );

        // add foreign key for table `tech_dep_difficulty`
        $this->addForeignKey(
            'fk-tech_dep_project-difficulty',
            'tech_dep_project',
            'difficulty',
            'tech_dep_difficulty',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `tech_dep_type_project`
        $this->dropForeignKey(
            'fk-tech_dep_project-type',
            'tech_dep_project'
        );

        // drops index for column `typeProject`
        $this->dropIndex(
            'idx-tech_dep_project-type',
            'tech_dep_project'
        );

        // drops foreign key for table `tech_dep_status_project`
        $this->dropForeignKey(
            'fk-tech_dep_project-status',
            'tech_dep_project'
        );

        // drops index for column `status`
        $this->dropIndex(
            'idx-tech_dep_project-status',
            'tech_dep_project'
        );

        // drops foreign key for table `tech_dep_priority_project`
        $this->dropForeignKey(
            'fk-tech_dep_project-priority',
            'tech_dep_project'
        );

        // drops index for column `priority`
        $this->dropIndex(
            'idx-tech_dep_project-priority',
            'tech_dep_project'
        );

        // drops foreign key for table `tech_dep_difficulty`
        $this->dropForeignKey(
            'fk-tech_dep_project-difficulty',
            'tech_dep_project'
        );

        // drops index for column `difficulty`
        $this->dropIndex(
            'idx-tech_dep_project-difficulty',
            'tech_dep_project'
        );

        $this->dropTable('tech_dep_project');
    }
}
