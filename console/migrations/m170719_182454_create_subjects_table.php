<?php

use yii\db\Migration;

/**
 * Handles the creation of table `subjects`.
 */
class m170719_182454_create_subjects_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `subjects` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№',
              `name` VARCHAR(255) NOT NULL COMMENT 'Название',
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            COMMENT = 'Наши темы'
        ");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('subjects');
    }
}
