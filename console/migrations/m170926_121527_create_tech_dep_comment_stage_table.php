<?php

use yii\db\Migration;

class m170926_121527_create_tech_dep_comment_stage_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('tech_dep_comment_stage', [
            'id' => $this->primaryKey(),
            'project' => $this->integer(),
            'stage' => $this->integer(),
            'author' => $this->integer(),
            'createdAt' => $this->integer(),
            'comment' => $this->text(),
        ]);

        // creates index for column `project`
        $this->createIndex(
            'idx-tech_dep_comment_stage-project',
            'tech_dep_comment_stage',
            'project'
        );

        // add foreign key for table `tech_dep_project`
        $this->addForeignKey(
            'fk-tech_dep_comment_stage-project',
            'tech_dep_comment_stage',
            'project',
            'tech_dep_project',
            'id',
            'CASCADE'
        );

        // creates index for column `stage`
        $this->createIndex(
            'idx-tech_dep_comment_stage-stage',
            'tech_dep_comment_stage',
            'stage'
        );

        // add foreign key for table `tech_dep_stages_project`
        $this->addForeignKey(
            'fk-tech_dep_comment_stage-stage',
            'tech_dep_comment_stage',
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
            'fk-tech_dep_comment_stage-project',
            'tech_dep_comment_stage'
        );

        // drops index for column `project`
        $this->dropIndex(
            'idx-tech_dep_comment_stage-project',
            'tech_dep_comment_stage'
        );

        // drops foreign key for table `tech_dep_stages_project`
        $this->dropForeignKey(
            'fk-tech_dep_comment_stage-stage',
            'tech_dep_comment_stage'
        );

        // drops index for column `stage`
        $this->dropIndex(
            'idx-tech_dep_comment_stage-stage',
            'tech_dep_comment_stage'
        );

        $this->dropTable('tech_dep_comment_stage');
    }
}
