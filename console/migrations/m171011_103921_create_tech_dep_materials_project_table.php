<?php

use yii\db\Migration;

class m171011_103921_create_tech_dep_materials_project_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('tech_dep_materials_project', [
            'id' => $this->primaryKey(),
            'project' => $this->integer(),
            'material' => $this->integer(),
            'quantity' => $this->float(),
            'position' => $this->smallInteger()
        ]);

        // creates index for column `project`
        $this->createIndex(
            'idx-tech_dep_materials_project-project',
            'tech_dep_materials_project',
            'project'
        );

        // add foreign key for table `tech_dep_project`
        $this->addForeignKey(
            'fk-tech_dep_materials_project-project',
            'tech_dep_materials_project',
            'project',
            'tech_dep_project',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        // drops foreign key for table `tech_dep_project`
        $this->dropForeignKey(
            'fk-tech_dep_materials_project-project',
            'tech_dep_materials_project'
        );

        // drops index for column `project`
        $this->dropIndex(
            'idx-tech_dep_materials_project-project',
            'tech_dep_materials_project'
        );

        $this->dropTable('tech_dep_materials_project');
    }
}
