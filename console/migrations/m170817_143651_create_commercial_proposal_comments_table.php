<?php

use yii\db\Migration;

/**
 * Handles the creation of table `commercial_proposals_comments`.
 */
class m170817_143651_create_commercial_proposal_comments_table extends Migration
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
            CREATE TABLE IF NOT EXISTS `commercial_proposal_comments` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№', 
              `commercial_proposal_id` INT NOT NULL COMMENT 'Коммерческое предложение',
              `status_id` INT NULL COMMENT 'Статус',
              `user_id` INT UNSIGNED NOT NULL COMMENT 'Автор',
              `text` TEXT NULL COMMENT 'Текст',
              `deadline` DATETIME NULL COMMENT 'Дедлайн',
              `created` DATETIME NOT NULL COMMENT 'Дата и время создания',
              PRIMARY KEY (`id`),
              INDEX `fk_cpc_status_id__cps_id_idx` (`status_id` ASC),
              INDEX `fk_cpc_user_id__user_user_id_idx` (`user_id` ASC),
              INDEX `fk_cpc_cp_id__cp_id_idx` (`commercial_proposal_id` ASC),
              CONSTRAINT `fk_cpc_status_id__cps_id`
                FOREIGN KEY (`status_id`)
                REFERENCES `commercial_proposal_statuses` (`id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cpc_user_id__user_user_id`
                FOREIGN KEY (`user_id`)
                REFERENCES `" . $old_db_name . "`.`user` (`user_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cpc_cp_id__cp_id`
                FOREIGN KEY (`commercial_proposal_id`)
                REFERENCES `commercial_proposals` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE)
        ");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('commercial_proposal_comments');
    }
}
