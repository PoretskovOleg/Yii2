<?php

use yii\db\Migration;

/**
 * Handles the creation of table `commercial_proposals`.
 */
class m170807_083115_create_commercial_proposals_table extends Migration
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
            CREATE TABLE IF NOT EXISTS `commercial_proposals` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№',
              `status_id` INT NOT NULL COMMENT 'Статус',
              `primary` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Основной',
              `order_id` INT NOT NULL COMMENT 'Заказ',
              `total` DECIMAL(20,2) DEFAULT 0 COMMENT 'Сумма',
              `payment_method` BOOLEAN NULL COMMENT 'Способ оплаты',
              `contractor_id` INT NOT NULL COMMENT 'Контрагент',
              `payer_organization_id` INT NULL DEFAULT NULL COMMENT 'Плательщик юр.лицо',
              `payer_contact_person_id` INT NULL DEFAULT NULL COMMENT 'Плательщик физ.лицо',
              `contact_person_id` INT NOT NULL COMMENT 'Контактное лицо',
              `subject_id` INT NOT NULL COMMENT 'Тема',
              `brand_id` INT NOT NULL COMMENT 'Бренд',
              `manager_id` INT(10) UNSIGNED NOT NULL COMMENT 'Менеджер',
              `template_id` INT NOT NULL COMMENT 'Шаблон',
              `organization_id` INT NOT NULL COMMENT 'Юридическое лицо',
              `signer_id` INT NOT NULL COMMENT 'Подписант',
              `prepayment_percentage` INT NOT NULL COMMENT 'Процент предоплаты',
              `term_days` INT NOT NULL COMMENT 'Срок в днях',
              `delivery` BOOLEAN NOT NULL COMMENT 'Доставка',
              `delivery_stock_id` INT NULL COMMENT 'Место отгрузки',
              `delivery_payment_type` INT NULL COMMENT 'Тип подсчёта доставки',
              `delivery_address` VARCHAR(255) NULL COMMENT 'Адрес доставки',
              `delivery_price` DECIMAL(20,2) NULL COMMENT 'Стоимость доставки',
              `note` TEXT NULL COMMENT 'Примечание',
              `attachments_pages_ids` VARCHAR(255) NULL COMMENT 'Дополнительные листы',
              `attachments_filenames` VARCHAR(255) NULL COMMENT 'Дополнительные файлы',
              `created` DATETIME NOT NULL COMMENT 'Дата и время создания',
              PRIMARY KEY (`id`),
              INDEX `fk_cp_brand_id__brands_id_idx` (`brand_id` ASC),
              INDEX `fk_cp_template_id__cpt_id_idx` (`template_id` ASC),
              INDEX `fk_cp_contractor_id__contractor_contractor_id_idx` (`contractor_id` ASC),
              INDEX `fk_cp_contact_person_id__contact_person_contact_person_id_idx` (`contact_person_id` ASC),
              INDEX `fk_cp_manager_id__user_user_id_idx` (`manager_id` ASC),
              INDEX `fk_cp_payer_organization_id__organization_idx` (`payer_organization_id` ASC),
              INDEX `fk_cp_payer_contact_person__contact_person_idx` (`payer_contact_person_id` ASC),
              INDEX `fk_cp_signer_id__signatory_signatory_id_idx` (`signer_id` ASC),
              INDEX `fk_cp_organization_id__organization_organization_id_idx` (`organization_id` ASC),
              INDEX `fk_cp_delivery_stock_id__stock_stock_id_idx` (`delivery_stock_id` ASC),
              INDEX `fk_cp_subject_id__order_theme_theme_id_idx` (`subject_id` ASC),
              INDEX `fk_cp_status_id__cp_statuses_id_idx` (`status_id` ASC),
              CONSTRAINT `fk_cp_brand_id__brands_id`
                FOREIGN KEY (`brand_id`)
                REFERENCES `brands` (`id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cp_template_id__cpt_id`
                FOREIGN KEY (`template_id`)
                REFERENCES `commercial_proposal_templates` (`id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cp_contractor_id__contractor_contractor_id`
                FOREIGN KEY (`contractor_id`)
                REFERENCES `" . $old_db_name . "`.`contractor` (`contractor_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cp_contact_person_id__contact_person_contact_person_id`
                FOREIGN KEY (`contact_person_id`)
                REFERENCES `" . $old_db_name . "`.`contact_person` (`contact_person_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cp_manager_id__user_user_id`
                FOREIGN KEY (`manager_id`)
                REFERENCES `" . $old_db_name . "`.`user` (`user_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cp_payer_organization_id__organization`
                FOREIGN KEY (`payer_organization_id`)
                REFERENCES `" . $old_db_name . "`.`organization` (`organization_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cp_payer_contact_person_id__contact_person_id`
                FOREIGN KEY (`payer_contact_person_id`)
                REFERENCES `" . $old_db_name . "`.`contact_person` (`contact_person_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cp_signer_id__signatory_signatory_id`
                FOREIGN KEY (`signer_id`)
                REFERENCES `" . $old_db_name . "`.`signatory` (`signatory_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cp_organization_id__organization_organization_id`
                FOREIGN KEY (`organization_id`)
                REFERENCES `" . $old_db_name . "`.`organization` (`organization_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cp_delivery_stock_id__stock_stock_id`
                FOREIGN KEY (`delivery_stock_id`)
                REFERENCES `" . $old_db_name . "`.`stock` (`stock_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cp_subject_id__order_theme_theme_id`
                FOREIGN KEY (`subject_id`)
                REFERENCES `" . $old_db_name . "`.`order_theme` (`theme_id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE,
              CONSTRAINT `fk_cp_status_id__cp_statuses_id`
                FOREIGN KEY (`status_id`)
                REFERENCES `commercial_proposal_statuses` (`id`)
                ON DELETE NO ACTION
                ON UPDATE CASCADE)
            ENGINE = InnoDB
            COMMENT = 'Коммерческие предложения'
        ");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('commercial_proposals');
    }
}
