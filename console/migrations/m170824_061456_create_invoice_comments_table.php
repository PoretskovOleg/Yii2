<?php

use yii\db\Migration;

/**
 * Handles the creation of table `invoice_comments`.
 */
class m170824_061456_create_invoice_comments_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        if (preg_match('/dbname=([^;]*)/', Yii::$app->old_db->dsn, $match)) {
            $old_db_name =  $match[1];
        } else {
            return 'Не могу получить имя старой базы';
        }

        $this->execute("
            CREATE TABLE IF NOT EXISTS `invoice_comments` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№', 
              `invoice_id` INT NOT NULL COMMENT 'Счёт',
              `status_id` INT NULL COMMENT 'Статус',
              `user_id` INT UNSIGNED NOT NULL COMMENT 'Автор',
              `text` TEXT NULL COMMENT 'Текст',
              `deadline` DATETIME NULL COMMENT 'Дедлайн',
              `created` DATETIME NOT NULL COMMENT 'Дата и время создания',
              PRIMARY KEY (`id`),
              INDEX `fk_ic_status_id__is_id_idx` (`status_id` ASC),
              INDEX `fk_ic_user_id__user_user_id_idx` (`user_id` ASC),
              INDEX `fk_ic_i_id__i_id_idx` (`invoice_id` ASC),
              CONSTRAINT `fk_ic_status_id__is_id`
                FOREIGN KEY (`status_id`)
                REFERENCES `invoice_statuses` (`id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ic_user_id__user_user_id`
                FOREIGN KEY (`user_id`)
                REFERENCES `" . $old_db_name . "`.`user` (`user_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ic_i_id__i_id`
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
        $this->dropTable('invoice_comments');
    }
}
