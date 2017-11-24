<?php

use yii\db\Migration;

class m171020_073519_create_production_order_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('production_order', [
            'id' => $this->primaryKey(),
            'number' => $this->string(),
            'createdAt' => $this->integer(),
            'finishedAt' => $this->integer(),
            'author' => $this->integer(),
            'good' => $this->integer(),
            'nameGood' => $this->string(),
            'order' => $this->string(),
            'priority' => $this->integer(),
            'target' => $this->integer(),
            'theme' => $this->integer(),
            'typeGood' => $this->integer(),
            'stage' => $this->integer(),
            'status' => $this->integer(),
            'responsible' => $this->integer(),
            'otk' => $this->integer(),
            'dedline' => $this->integer(),
            'countOrder' => $this->smallInteger(),
            'countStock' => $this->smallInteger(),
            'notice' => $this->string(),
            'sequence' => $this->smallInteger(),
            'section' => $this->string(),
            'posSection' => $this->smallInteger()
        ]);

        // creates index for column `priority`
        $this->createIndex(
            'idx-production_order-priority',
            'production_order',
            'priority'
        );

        // add foreign key for table `production_priority`
        $this->addForeignKey(
            'fk-production_order-priority',
            'production_order',
            'priority',
            'production_priority',
            'id',
            'CASCADE'
        );

        // creates index for column `target`
        $this->createIndex(
            'idx-production_order-target',
            'production_order',
            'target'
        );

        // add foreign key for table `production_target`
        $this->addForeignKey(
            'fk-production_order-target',
            'production_order',
            'target',
            'production_target',
            'id',
            'CASCADE'
        );

        // creates index for column `theme`
        $this->createIndex(
            'idx-production_order-theme',
            'production_order',
            'theme'
        );

        // add foreign key for table `production_theme`
        $this->addForeignKey(
            'fk-production_order-theme',
            'production_order',
            'theme',
            'production_theme',
            'id',
            'CASCADE'
        );

        // creates index for column `typeGood`
        $this->createIndex(
            'idx-production_order-typeGood',
            'production_order',
            'typeGood'
        );

        // add foreign key for table `production_type_good`
        $this->addForeignKey(
            'fk-production_order-typeGood',
            'production_order',
            'typeGood',
            'production_type_good',
            'id',
            'CASCADE'
        );

        // creates index for column `stage`
        $this->createIndex(
            'idx-production_order-stage',
            'production_order',
            'stage'
        );

        // add foreign key for table `production_stage_order`
        $this->addForeignKey(
            'fk-production_order-stage',
            'production_order',
            'stage',
            'production_stage_order',
            'id',
            'CASCADE'
        );

        // creates index for column `status`
        $this->createIndex(
            'idx-production_order-status',
            'production_order',
            'status'
        );

        // add foreign key for table `production_status_order`
        $this->addForeignKey(
            'fk-production_order-status',
            'production_order',
            'status',
            'production_status_order',
            'id',
            'CASCADE'
        );

        $this->addCommentOnColumn('production_order', 'number', 'Номер з/н');
        $this->addCommentOnColumn('production_order', 'order', 'Номер счета');
        $this->addCommentOnColumn('production_order', 'sequence', 'Порядок выполнения з/н');
        $this->addCommentOnColumn('production_order', 'section', 'Группа для сортировки');
        $this->addCommentOnColumn('production_order', 'posSection', 'Порядок группы');
    }

    public function safeDown()
    {
        $this->dropCommentFromColumn('production_order', 'posSection');
        $this->dropCommentFromColumn('production_order', 'section');
        $this->dropCommentFromColumn('production_order', 'sequence');
        $this->dropCommentFromColumn('production_order', 'order');
        $this->dropCommentFromColumn('production_order', 'number');

        // drops foreign key for table `production_priority`
        $this->dropForeignKey(
            'fk-production_order-priority',
            'production_order'
        );

        // drops index for column `priority`
        $this->dropIndex(
            'idx-production_order-priority',
            'production_order'
        );

        // drops foreign key for table `production_target`
        $this->dropForeignKey(
            'fk-production_order-target',
            'production_order'
        );

        // drops index for column `target`
        $this->dropIndex(
            'idx-production_order-target',
            'production_order'
        );

        // drops foreign key for table `production_theme`
        $this->dropForeignKey(
            'fk-production_order-theme',
            'production_order'
        );

        // drops index for column `theme`
        $this->dropIndex(
            'idx-production_order-theme',
            'production_order'
        );

        // drops foreign key for table `production_type_good`
        $this->dropForeignKey(
            'fk-production_order-typeGood',
            'production_order'
        );

        // drops index for column `typeGood`
        $this->dropIndex(
            'idx-production_order-typeGood',
            'production_order'
        );

        // drops foreign key for table `production_stage_order`
        $this->dropForeignKey(
            'fk-production_order-stage',
            'production_order'
        );

        // drops index for column `stage`
        $this->dropIndex(
            'idx-production_order-stage',
            'production_order'
        );

        // drops foreign key for table `production_status_order`
        $this->dropForeignKey(
            'fk-production_order-status',
            'production_order'
        );

        // drops index for column `status`
        $this->dropIndex(
            'idx-production_order-status',
            'production_order'
        );

        $this->dropTable('production_order');
    }
}
