<?php

use yii\db\Migration;

class m171002_112633_add_archive_column_to_tech_dep_project_table extends Migration
{
    public function up()
    {
        $this->addColumn('tech_dep_project', 'archive', $this->smallInteger());
    }

    public function down()
    {
        $this->dropColumn('tech_dep_project', 'archive');
    }
}
