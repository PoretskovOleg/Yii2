<?php

use yii\db\Migration;

/**
 * Handles the creation of table `invoice_goods`.
 */
class m170824_061121_create_invoice_goods_table extends Migration
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
            CREATE TABLE IF NOT EXISTS `invoice_goods` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№',
              `index` INT NOT NULL COMMENT 'Порядковый номер',
              `invoice_id` INT NOT NULL COMMENT 'Счёт',
              `good_id` INT NULL COMMENT 'Связанный товар',
              `name` VARCHAR(255) NOT NULL COMMENT 'Наименование',
              `quantity` INT NOT NULL COMMENT 'Кол-во',
              `unit_id` INT NULL COMMENT 'Ед. изм',
              `price` DECIMAL(20,2) NULL COMMENT 'Себестоимость',
              `delivery_period` INT NOT NULL DEFAULT 0 COMMENT 'Срок, дней',
              `mrc_percent` DECIMAL(5,2) NULL COMMENT 'МРЦ',
              `base_price_percent` DECIMAL(5,2) NULL COMMENT 'Базовая цена',
              `discount` DECIMAL(5,2) NULL COMMENT 'Скидка',
              `end_price` DECIMAL(20,2) NOT NULL COMMENT 'Окончательная цена',
              `margin_percent` DECIMAL(5,2) NULL COMMENT 'Маржа',
              `weight` DECIMAL(20,2) NOT NULL DEFAULT 0 COMMENT 'Вес, кг',
              `volume` DECIMAL(20,2) NOT NULL DEFAULT 0 COMMENT 'Объём, м3',
              PRIMARY KEY (`id`),
              INDEX `fk_ig_i_id__i_id_idx` (`invoice_id` ASC),
              INDEX `fk_ig_unit_id__list_unit_list_unit_id_idx` (`unit_id` ASC),
              INDEX `fk_ig_good_id__goods_goods_id_idx` (`good_id` ASC),
              CONSTRAINT `fk_ig_i_id__i_id`
                FOREIGN KEY (`invoice_id`)
                REFERENCES `invoices` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT `fk_ig_unit_id__list_unit_list_unit_id`
                FOREIGN KEY (`unit_id`)
                REFERENCES `" . $old_db_name . "`.`list_unit` (`list_unit_id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_ig_good_id__goods_goods_id`
                FOREIGN KEY (`good_id`)
                REFERENCES `" . $old_db_name . "`.`goods` (`goods_id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
        ");
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('invoice_goods');
    }
}
