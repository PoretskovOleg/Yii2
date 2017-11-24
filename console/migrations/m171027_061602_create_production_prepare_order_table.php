<?php

use yii\db\Migration;

class m171027_061602_create_production_prepare_order_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('production_prepare_order', [
            'id' => $this->primaryKey(),
            'order' => $this->integer(),
            'stage' => $this->integer(),
            'isPrepare' => $this->smallInteger(1),
        ]);

        // creates index for column `order`
        $this->createIndex(
            'idx-production_prepare_order-order',
            'production_prepare_order',
            'order'
        );

        // add foreign key for table `production_order`
        $this->addForeignKey(
            'fk-production_prepare_order-order',
            'production_prepare_order',
            'order',
            'production_order',
            'id',
            'CASCADE'
        );

        // creates index for column `stage`
        $this->createIndex(
            'idx-production_prepare_order-stage',
            'production_prepare_order',
            'stage'
        );

        // add foreign key for table `production_stage_prepare`
        $this->addForeignKey(
            'fk-production_prepare_order-stage',
            'production_prepare_order',
            'stage',
            'production_stage_prepare',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `production_order`
        $this->dropForeignKey(
            'fk-production_prepare_order-order',
            'production_prepare_order'
        );

        // drops index for column `order`
        $this->dropIndex(
            'idx-production_prepare_order-order',
            'production_prepare_order'
        );

        // drops foreign key for table `production_stage_prepare`
        $this->dropForeignKey(
            'fk-production_prepare_order-stage',
            'production_prepare_order'
        );

        // drops index for column `stage`
        $this->dropIndex(
            'idx-production_prepare_order-stage',
            'production_prepare_order'
        );

        $this->dropTable('production_prepare_order');
    }
}
