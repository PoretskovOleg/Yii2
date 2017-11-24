<?php

use yii\db\Migration;

/**
 * Handles the creation of table `rules`.
 */
class m170803_065801_create_rules_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('rules', [
            'old_rule_id' => $this->primaryKey()->comment('Номер правила в старой базе'),
            'rule_id' => $this->integer()->comment('Номер правила внутри модуля'),
            'module' => $this->string(255)->comment('Название модуля'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('rules');
    }
}
