<?php

use yii\db\Migration;

/**
 * Handles adding type to table `attachments`.
 */
class m170807_052828_add_type_column_to_attachments_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('attachments', 'type', $this->integer()->notNull()->comment('Тип файла')->after('id'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('attachments', 'type');
    }
}
