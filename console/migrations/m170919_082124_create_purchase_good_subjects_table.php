<?php

use yii\db\Migration;

/**
 * Handles the creation of table `purchase_good_subjects`.
 */
class m170919_082124_create_purchase_good_subjects_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        Yii::$app->old_db->createCommand()->createTable('purchase_good_subjects', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ])->execute();

        Yii::$app->old_db->createCommand()->addColumn('goods', 'subject_id', $this->integer())->execute();
        Yii::$app->old_db->createCommand()->createIndex('goods_subject_id__pgs_id_idx', 'goods', 'subject_id')->execute();
        Yii::$app->old_db->createCommand()->addForeignKey(
                'fk_goods_subject_id__pgs_id',
                'goods',
                'subject_id',
                'purchase_good_subjects',
                'id',
                'NO ACTION',
                'NO ACTION'
            )->execute();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        Yii::$app->old_db->createCommand()->dropForeignKey('fk_goods_subject_id__pgs_id', 'goods')->execute();
        Yii::$app->old_db->createCommand()->dropIndex('goods_subject_id__pgs_id_idx', 'goods')->execute();
        Yii::$app->old_db->createCommand()->dropColumn('goods', 'subject_id')->execute();
        Yii::$app->old_db->createCommand()->dropTable('purchase_good_subjects')->execute();
    }
}
