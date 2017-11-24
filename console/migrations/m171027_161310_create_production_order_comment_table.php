<?php

use yii\db\Migration;

class m171027_161310_create_production_order_comment_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('production_order_comment', [
            'id' => $this->primaryKey(),
            'order' => $this->integer(),
            'createdAt' => $this->integer(),
            'author' => $this->integer(),
            'comment' => $this->text(),
        ]);

        // creates index for column `order`
        $this->createIndex(
            'idx-production_order_comment-order',
            'production_order_comment',
            'order'
        );

        // add foreign key for table `production_order`
        $this->addForeignKey(
            'fk-production_order_comment-order',
            'production_order_comment',
            'order',
            'production_order',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `production_order`
        $this->dropForeignKey(
            'fk-production_order_comment-order',
            'production_order_comment'
        );

        // drops index for column `order`
        $this->dropIndex(
            'idx-production_order_comment-order',
            'production_order_comment'
        );

        $this->dropTable('production_order_comment');
    }
}
