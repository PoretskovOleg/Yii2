<?php

use yii\db\Migration;

class m170920_114028_create_tech_dep_comment_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('tech_dep_comment', [
            'id' => $this->primaryKey(),
            'project' => $this->integer(),
            'createdAt' => $this->integer(),
            'author' => $this->integer(),
            'comment' => $this->string(),
        ]);

        // creates index for column `project`
        $this->createIndex(
            'idx-tech_dep_comment-project',
            'tech_dep_comment',
            'project'
        );

        // add foreign key for table `tech_dep_project`
        $this->addForeignKey(
            'fk-tech_dep_comment-project',
            'tech_dep_comment',
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
            'fk-tech_dep_comment-project',
            'tech_dep_comment'
        );

        // drops index for column `project`
        $this->dropIndex(
            'idx-tech_dep_comment-project',
            'tech_dep_comment'
        );

        $this->dropTable('tech_dep_comment');
    }
}
