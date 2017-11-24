<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `subjects`.
 */
class m170802_151640_drop_subjects_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropTable('subjects');
    }

    /**
     * @inheritdoc
     */
    public function down()
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
}
