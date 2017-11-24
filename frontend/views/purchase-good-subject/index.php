<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Темы закупок';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="index">
    <div class="box box-default">
        <?php if (Yii::$app->user->identity->checkRule('purchase-good-subjects', 2)): ?>
            <div class="box-header with-border">
                <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-primary pull-right']) ?>
            </div>
        <?php endif; ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout'=>'<div class="box-body no-padding table-responsive">{items}</div><div class="box-footer">{pager}</div>',
            'columns' => [
                'id' => [
                    'label' => '№',
                    'format' => 'html',
                    'options' => ['style' => 'width: 5%'],
                    'value' => function($data) {
                        if (Yii::$app->user->identity->checkRule('purchase-good-subjects', 2)) {
                            return Html::a($data->id, ['update', 'id' => $data->id]);
                        }
                        return $data->id;
                    },
                ],
                'name',
            ],
        ]) ?>
    </div>
</div>
