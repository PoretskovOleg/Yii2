<?php

use yii\db\Migration;

/**
 * Handles the creation of table `commercial_proposal_statuses`.
 */
class m170807_083022_create_commercial_proposal_statuses_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS `commercial_proposal_statuses` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '№',
              `name` VARCHAR(255) NOT NULL COMMENT 'Название',
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            COMMENT = 'Статусы коммерческих предложений'
        ");

        $this->batchInsert('commercial_proposal_statuses', ['name'], [
            ['name' => 'Новый'],
            ['name' => 'Согласование'],
            ['name' => 'Исправление'],
            ['name' => 'Согласован'],
            ['name' => 'Отправлен'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('commercial_proposal_statuses');
    }
}
