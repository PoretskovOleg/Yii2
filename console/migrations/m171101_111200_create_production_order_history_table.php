<?php

use yii\db\Migration;

class m171101_111200_create_production_order_history_table extends Migration
{
    public function up()
    {
        $this->createTable('production_order_history', [
            'id' => $this->primaryKey(),
            'order' => $this->integer(),
            'status' => $this->integer(),
            'createdAt' => $this->integer(),
            'author' => $this->integer(),
            'comment' => $this->string(),
        ]);

        // creates index for column `order`
        $this->createIndex(
            'idx-production_order_history-order',
            'production_order_history',
            'order'
        );

        // add foreign key for table `production_order`
        $this->addForeignKey(
            'fk-production_order_history-order',
            'production_order_history',
            'order',
            'production_order',
            'id',
            'CASCADE'
        );

        // creates index for column `status`
        $this->createIndex(
            'idx-production_order_history-status',
            'production_order_history',
            'status'
        );

        // add foreign key for table `production_status_order`
        $this->addForeignKey(
            'fk-production_order_history-status',
            'production_order_history',
            'status',
            'production_status_order',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        // drops foreign key for table `production_order`
        $this->dropForeignKey(
            'fk-production_order_history-order',
            'production_order_history'
        );

        // drops index for column `order`
        $this->dropIndex(
            'idx-production_order_history-order',
            'production_order_history'
        );

        // drops foreign key for table `production_status_order`
        $this->dropForeignKey(
            'fk-production_order_history-status',
            'production_order_history'
        );

        // drops index for column `status`
        $this->dropIndex(
            'idx-production_order_history-status',
            'production_order_history'
        );

        $this->dropTable('production_order_history');
    }
}
