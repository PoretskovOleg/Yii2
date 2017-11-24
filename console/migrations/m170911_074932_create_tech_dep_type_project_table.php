<?php

use yii\db\Migration;

class m170911_074932_create_tech_dep_type_project_table extends Migration
{
    public function up()
    {
        $this->createTable('tech_dep_type_project', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'fullName' => $this->string(),
        ]);

        $this->batchInsert('tech_dep_type_project', ['name', 'fullName'], [
            ['Тип. изд.', 'Типовое изделие'],
            ['Тип. изд. изм.', 'Типовое изделие с изменениями'],
            ['Инд. заказ', 'Индивидуальный заказ'],
            ['Для комм.', 'Для коммерческих предложений']
        ]);
    }

    public function down()
    {
        $this->dropTable('tech_dep_type_project');
    }
}
