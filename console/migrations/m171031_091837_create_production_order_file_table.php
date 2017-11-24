<?php

use yii\db\Migration;

class m171031_091837_create_production_order_file_table extends Migration
{
    public function up()
    {
        $this->createTable('production_order_file', [
            'id' => $this->primaryKey(),
            'order' => $this->integer(),
            'stage' => $this->integer(),
            'name' => $this->string(),
        ]);

        // creates index for column `order`
        $this->createIndex(
            'idx-production_order_file-order',
            'production_order_file',
            'order'
        );

        // add foreign key for table `production_order`
        $this->addForeignKey(
            'fk-production_order_file-order',
            'production_order_file',
            'order',
            'production_order',
            'id',
            'CASCADE'
        );

        // creates index for column `stage`
        $this->createIndex(
            'idx-production_order_file-stage',
            'production_order_file',
            'stage'
        );

        // add foreign key for table `production_stage_order`
        $this->addForeignKey(
            'fk-production_order_file-stage',
            'production_order_file',
            'stage',
            'production_stage_order',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        // drops foreign key for table `production_order`
        $this->dropForeignKey(
            'fk-production_order_file-order',
            'production_order_file'
        );

        // drops index for column `order`
        $this->dropIndex(
            'idx-production_order_file-order',
            'production_order_file'
        );

        // drops foreign key for table `production_stage_order`
        $this->dropForeignKey(
            'fk-production_order_file-stage',
            'production_order_file'
        );

        // drops index for column `stage`
        $this->dropIndex(
            'idx-production_order_file-stage',
            'production_order_file'
        );

        $this->dropTable('production_order_file');
    }
}
