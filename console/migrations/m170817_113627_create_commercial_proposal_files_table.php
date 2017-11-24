<?php

use yii\db\Migration;

/**
 * Handles the creation of table `commercial_proposal_files`.
 */
class m170817_113627_create_commercial_proposal_files_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `commercial_proposal_files` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№',
              `commercial_proposal_id` INT NOT NULL COMMENT 'Коммерческое предложение',
              `filename` VARCHAR(255) NOT NULL COMMENT 'Имя файла',
              PRIMARY KEY (`id`),
              INDEX `fk_cpf_cp_id__cp_id_idx` (`commercial_proposal_id` ASC),
              CONSTRAINT `fk_cpf_cp_id__cp_id`
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
        $this->dropTable('commercial_proposal_files');
    }
}
