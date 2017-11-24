<?php

use yii\db\Migration;

class m171013_063246_add_rule_delete_for_tech_dep_module extends Migration
{
    public function safeUp()
    {
        //название модуля для отображения в старой админке
        $module_title = 'Модуль Техотдел';
        //название модуля для использования в checkRule($module_name, $rule_id)
        $module_name = frontend\controllers\TechDepController::MODULE_NAME;
        //массив правил [['id' => '1', 'name' => 'Правило 1'], ['id' => '2', 'name'...
        $rule = frontend\controllers\TechDepController::getUserRules()[7];

        $module_id = Yii::$app->old_db->createCommand('
                SELECT module_id FROM module WHERE module_name = :module_name
            ', ['module_name' => $module_title])->queryScalar();

        $fields = [];
        $fields[] = [
            'module_id' => $module_id,
            'rule_name' => $rule['name'],
            'sort_status' => 0,
        ];

        Yii::$app->old_db->createCommand()
            ->batchInsert('rule', ['module_id', 'rule_name', 'sort_status'], $fields)
            ->execute();

        $old_id = Yii::$app->old_db->getLastInsertID();

        $fields = [];
        $fields[] = [
            'old_rule_id' => $old_id,
            'rule_id' => $rule['id'],
            'module' => $module_name,
        ];

        Yii::$app->db->createCommand()
            ->batchInsert('rules', ['old_rule_id', 'rule_id', 'module'], $fields)
            ->execute();
    }

    public function safeDown()
    {
        //название модуля, соответствующее названию из функции safeUp
        $module_name = frontend\controllers\TechDepController::MODULE_NAME;

        $old_rule_ids = Yii::$app->db->createCommand('
                SELECT old_rule_id FROM rules WHERE module = :module AND rule_id =:rule_id
            ', ['module' => $module_name, 'rule_id' => 8])->queryColumn();

        if (!is_array($old_rule_ids)) {
            return false;
        }

        Yii::$app->db->createCommand()->delete('rules', ['and', ['=', 'module', $module_name], ['in', 'rule_id', [8]]])->execute();

        $deleteCommand = Yii::$app->old_db->createCommand();
        foreach ($old_rule_ids as $id) {
            $deleteCommand->delete('post_rule', ['rule_id' => $id])->execute();
            $deleteCommand->delete('user_rule', ['rule_id' => $id])->execute();
        }

        $module_id = Yii::$app->old_db->createCommand('
                SELECT module_id FROM rule WHERE rule_id = :rule_id
            ', ['rule_id' => array_pop($old_rule_ids)])->queryScalar();

        Yii::$app->old_db->createCommand()->delete('rule', ['module_id' => $module_id])->execute();
    }
}
