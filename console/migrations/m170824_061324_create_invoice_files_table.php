<?php

use yii\db\Migration;

/**
 * Handles the creation of table `invoice_files`.
 */
class m170824_061324_create_invoice_files_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `invoice_files` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№',
              `invoice_id` INT NOT NULL COMMENT 'Счет',
              `filename` VARCHAR(255) NOT NULL COMMENT 'Имя файла',
              PRIMARY KEY (`id`),
              INDEX `fk_if_i_id__i_id_idx` (`invoice_id` ASC),
              CONSTRAINT `fk_if_i_id__i_id`
                FOREIGN KEY (`invoice_id`)
                REFERENCES `invoices` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
        ");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('invoice_files');
    }
}
