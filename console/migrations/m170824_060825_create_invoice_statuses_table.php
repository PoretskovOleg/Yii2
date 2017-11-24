<?php

use yii\db\Migration;

/**
 * Handles the creation of table `invoice_statuses`.
 */
class m170824_060825_create_invoice_statuses_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `invoice_statuses` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№',
              `name` VARCHAR(255) NOT NULL COMMENT 'Название',
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            COMMENT = 'Статусы счетов'
        ");

        $this->batchInsert('invoice_statuses', ['name'], [
            ['name' => 'Новый'],
            ['name' => 'Согласование'],
            ['name' => 'Исправление'],
            ['name' => 'Согласован'],
            ['name' => 'Отправлен'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('invoice_statuses');
    }
}
