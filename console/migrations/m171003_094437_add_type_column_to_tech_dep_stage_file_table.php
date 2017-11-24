<?php

use yii\db\Migration;

class m171003_094437_add_type_column_to_tech_dep_stage_file_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('tech_dep_stage_file', 'type', $this->integer());

        // creates index for column `type`
        $this->createIndex(
            'idx-tech_dep_stage_file-type',
            'tech_dep_stage_file',
            'type'
        );

        // add foreign key for table `tech_dep_type_file_stage`
        $this->addForeignKey(
            'fk-tech_dep_stage_file-type',
            'tech_dep_stage_file',
            'type',
            'tech_dep_type_file_stage',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `tech_dep_type_file_stage`
        $this->dropForeignKey(
            'fk-tech_dep_stage_file-type',
            'tech_dep_stage_file'
        );

        // drops index for column `type`
        $this->dropIndex(
            'idx-tech_dep_stage_file-type',
            'tech_dep_stage_file'
        );

        $this->dropColumn('tech_dep_stage_file', 'type');
    }
}
