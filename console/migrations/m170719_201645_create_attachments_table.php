<?php

use yii\db\Migration;

/**
 * Handles the creation of table `attachments`.
 */
class m170719_201645_create_attachments_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `attachments` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№',
              `name` VARCHAR(255) NOT NULL COMMENT 'Название',
              `filename` VARCHAR(255) NOT NULL COMMENT 'Имя файла',
              PRIMARY KEY (`id`))
            COMMENT = 'Прикрепляемые файлы'
        ");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('attachments');
    }
}
