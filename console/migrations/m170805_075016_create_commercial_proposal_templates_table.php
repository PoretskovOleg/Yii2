<?php

use yii\db\Migration;

/**
 * Handles the creation of table `commercial_proposal_templates`.
 */
class m170805_075016_create_commercial_proposal_templates_table extends Migration
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
            CREATE TABLE IF NOT EXISTS `commercial_proposal_templates` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№',
              `active` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Активен',
              `name` VARCHAR(255) NOT NULL COMMENT 'Название',
              `subject_id` INT NOT NULL COMMENT 'Тема',
              `amount_greater_than` FLOAT NOT NULL COMMENT 'Сумма больше, руб.',
              `amount_less_than` FLOAT NOT NULL COMMENT 'Сумма меньше, руб.',
              `needs_approval_by_ids` VARCHAR(255) NULL COMMENT 'Утверждается кем',
              `organization_id` INT NOT NULL COMMENT 'Юридическое лицо',
              `signer_id` INT NOT NULL COMMENT 'Подписант',
              `prepayment_percentage` INT NOT NULL COMMENT 'Процент предоплаты',
              `term_days` INT NOT NULL COMMENT 'Срок в днях',
              `delivery_stock_id` INT NOT NULL COMMENT 'Место отгрузки',
              `note` TEXT NULL COMMENT 'Примечание',
              `attachments_pages_ids` VARCHAR(255) NULL COMMENT 'Дополнительные листы',
              `promos_filenames` VARCHAR(255) NULL COMMENT 'Рекламные картинки',
              `created` DATETIME NOT NULL COMMENT 'Дата и время создания',
              PRIMARY KEY (`id`),
              KEY `idx_cpt_organization_id__organization_organization_id` (`organization_id`),
              CONSTRAINT `fk_cpt_organization_id__organization_organization_id`
                FOREIGN KEY (`organization_id`)
                REFERENCES `" . $old_db_name . "`.`organization` (`organization_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              KEY `idx_cpt_subject_id__order_theme_theme_id` (`subject_id`),
              CONSTRAINT `fk_cpt_subject_id__order_theme_theme_id`
                FOREIGN KEY (`subject_id`)
                REFERENCES `" . $old_db_name . "`.`order_theme` (`theme_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              KEY `idx_cpt_signer_id__signatory_signatory_id` (`signer_id`),
              CONSTRAINT `fk_cpt_signer_id__signatory_signatory_id`
                FOREIGN KEY (`signer_id`)
                REFERENCES `" . $old_db_name . "`.`signatory` (`signatory_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              KEY `idx_cpt_delivery_stock_id__stock_stock_id` (`delivery_stock_id`),
              CONSTRAINT `fk_cpt_delivery_stock_id__stock_stock_id`
                FOREIGN KEY (`delivery_stock_id`)
                REFERENCES `" . $old_db_name . "`.`stock` (`stock_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            COMMENT = 'Шаблоны коммерческих предложений'
        ");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey(
            'fk_cpt_delivery_stock_id__stock_stock_id',
            'commercial_proposal_templates'
        );

        $this->dropIndex(
            'idx_cpt_delivery_stock_id__stock_stock_id',
            'commercial_proposal_templates'
        );

        $this->dropForeignKey(
            'fk_cpt_subject_id__order_theme_theme_id',
            'commercial_proposal_templates'
        );

        $this->dropIndex(
            'idx_cpt_subject_id__order_theme_theme_id',
            'commercial_proposal_templates'
        );

        $this->dropForeignKey(
            'fk_cpt_organization_id__organization_organization_id',
            'commercial_proposal_templates'
        );

        $this->dropIndex(
            'idx_cpt_organization_id__organization_organization_id',
            'commercial_proposal_templates'
        );

        $this->dropTable('commercial_proposal_templates');
    }
}
