<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Шаблоны счетов';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="invoicetemplate-index">
    <div class="box box-default">
        <div class="box-header with-border">
            <?php if (Yii::$app->user->identity->checkRule('invoice-templates', 2)): ?>
                <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-primary pull-right']) ?>
            <?php endif; ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '<div class="box-body no-padding table-responsive">{items}</div><div class="box-footer">{pager}</div>',
            'columns' => [
                'id' => [
                    'label' => '№',
                    'format' => 'html',
                    'value' => function($data) {
                        if (Yii::$app->user->identity->checkRule('invoice-templates', 2)) {
                            return Html::a($data->id, ['update', 'id' => $data->id]);
                        }
                        return $data->id;
                    },
                ],
                'name',
                'subject' => [
                    'label' => 'Тема',
                    'value' => function($data) {
                        return $data->subject->name;
                    },
                ],
                'organization'  => [
                    'label' => 'Юр. лицо',
                    'value' => function($data) {
                        return $data->organization->organization_name;
                    },
                ],
                'needs_approval_by' => [
                    'label' => 'Утверждение',
                    'format' => 'html',
                    'value' => function($data) {
                        $approvers = '';
                        if (!empty($data->needs_approval_by_ids)) {
                            $approvers_ids = unserialize($data->needs_approval_by_ids);
                            foreach ($approvers_ids as $id) {
                                $approvers .= \common\models\Old\Position::findOne(['post_id' => $id])->post_name . '<br>';
                            }
                        }
                        return $approvers;
                    },
                ],
                'created' => [
                    'label' => 'Создан',
                    'value' => function($data) {
                        return (new \DateTime($data->created))->format('d.m.y H:i');
                    }
                ],
                'test' => [
                    'label' => 'Тест',
                    'format' => 'raw',
                    'value' => function($data) {
                        return Html::a('PDF', 'get-pdf?id=' . $data->id, ['target' => '_blank']);
                    },
                ]
            ],
        ]); ?>
</div>
