<?php

use yii\db\Migration;

class m171020_061034_create_production_type_good_table extends Migration
{
    public function up()
    {
        $this->createTable('production_type_good', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->batchInsert('production_type_good', ['name'], [
            ['Типовое'],
            ['Индивидуальное'],
        ]);
    }

    public function down()
    {
        $this->dropTable('production_type_good');
    }
}
