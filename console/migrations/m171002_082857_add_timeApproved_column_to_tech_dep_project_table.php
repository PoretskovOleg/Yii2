<?php

use yii\db\Migration;

class m171002_082857_add_timeApproved_column_to_tech_dep_project_table extends Migration
{
    public function up()
    {
        $this->addColumn('tech_dep_project', 'timeApproved', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('tech_dep_project', 'timeApproved');
    }
}
