<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brands`.
 */
class m170718_140219_create_brands_table extends Migration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `brands` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№',
              `title` VARCHAR(255) NOT NULL COMMENT 'Название',
              `slogan` TINYTEXT NULL COMMENT 'Девиз',
              `city` VARCHAR(255) NULL COMMENT 'Город',
              `address` TINYTEXT NULL COMMENT 'Адрес',
              `phone` VARCHAR(255) NULL COMMENT 'Телефон',
              `federal_phone` VARCHAR(255) NULL COMMENT 'Телефон 8-800',
              `website` VARCHAR(255) NULL COMMENT 'Сайт',
              `email` VARCHAR(255) NULL COMMENT 'Email',
              `logo_filename` VARCHAR(255) NULL COMMENT 'Имя файла логотипа',
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            COMMENT = 'Наши бренды';
        ");
    }

    public function down()
    {
        $this->dropTable('brands');
    }
}
