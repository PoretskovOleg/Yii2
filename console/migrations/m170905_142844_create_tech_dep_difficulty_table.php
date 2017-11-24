<?php

use yii\db\Migration;

class m170905_142844_create_tech_dep_difficulty_table extends Migration
{
    public function up()
    {
        $this->createTable('tech_dep_difficulty', [
            'id' => $this->primaryKey(),
            'difficulty' => $this->smallInteger(2),
            'stageName' => $this->string(15),
            'project' => $this->smallInteger(5),
            'techTask' => $this->smallInteger(5),
            'calc1' => $this->smallInteger(5),
            'calc2' => $this->smallInteger(5),
            'plan' => $this->smallInteger(5),
            'calcTech' => $this->smallInteger(5),
            'model' => $this->smallInteger(5),
            'draw' => $this->smallInteger(5),
            'spec' => $this->smallInteger(5),
            'materials' => $this->smallInteger(5),
            'tools' => $this->smallInteger(5),
            'techMap' => $this->smallInteger(5),
            'passport' => $this->smallInteger(5),
        ]);

        $this->batchInsert('tech_dep_difficulty', ['difficulty', 'stageName'], [
            ['1', 'ознакомление'],
            ['1', 'чистое'],
            ['1', 'дедлайн'],
            ['2', 'ознакомление'],
            ['2', 'чистое'],
            ['2', 'дедлайн'],
            ['3', 'ознакомление'],
            ['3', 'чистое'],
            ['3', 'дедлайн'],
            ['4', 'ознакомление'],
            ['4', 'чистое'],
            ['4', 'дедлайн'],
            ['5', 'ознакомление'],
            ['5', 'чистое'],
            ['5', 'дедлайн'],
        ]);
    }

    public function down()
    {
        $this->dropTable('tech_dep_difficulty');
    }
}
