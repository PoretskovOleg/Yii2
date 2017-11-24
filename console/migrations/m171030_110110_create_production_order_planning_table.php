<?php

use yii\db\Migration;

class m171030_110110_create_production_order_planning_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('production_order_planning', [
            'id' => $this->primaryKey(),
            'order' => $this->integer(),
            'stage' => $this->integer(),
            'status' => $this->integer(),
        ]);

        // creates index for column `order`
        $this->createIndex(
            'idx-production_order_planning-order',
            'production_order_planning',
            'order'
        );

        // add foreign key for table `production_order`
        $this->addForeignKey(
            'fk-production_order_planning-order',
            'production_order_planning',
            'order',
            'production_order',
            'id',
            'CASCADE'
        );

        // creates index for column `stage`
        $this->createIndex(
            'idx-production_order_planning-stage',
            'production_order_planning',
            'stage'
        );

        // add foreign key for table `production_stage_order`
        $this->addForeignKey(
            'fk-production_order_planning-stage',
            'production_order_planning',
            'stage',
            'production_stage_order',
            'id',
            'CASCADE'
        );

        // creates index for column `status`
        $this->createIndex(
            'idx-production_order_planning-status',
            'production_order_planning',
            'status'
        );

        // add foreign key for table `production_status_stage_order`
        $this->addForeignKey(
            'fk-production_order_planning-status',
            'production_order_planning',
            'status',
            'production_status_stage_order',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `production_order`
        $this->dropForeignKey(
            'fk-production_order_planning-order',
            'production_order_planning'
        );

        // drops index for column `order`
        $this->dropIndex(
            'idx-production_order_planning-order',
            'production_order_planning'
        );

        // drops foreign key for table `production_stage_order`
        $this->dropForeignKey(
            'fk-production_order_planning-stage',
            'production_order_planning'
        );

        // drops index for column `stage`
        $this->dropIndex(
            'idx-production_order_planning-stage',
            'production_order_planning'
        );

        // drops foreign key for table `production_status_stage_order`
        $this->dropForeignKey(
            'fk-production_order_planning-status',
            'production_order_planning'
        );

        // drops index for column `status`
        $this->dropIndex(
            'idx-production_order_planning-status',
            'production_order_planning'
        );

        $this->dropTable('production_order_planning');
    }
}
