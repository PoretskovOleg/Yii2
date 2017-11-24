<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Наши бренды';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="brand-index">

    <div class="box box-default">
        <?php if (Yii::$app->user->identity->checkRule('brands', 2)): ?>
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
                    'value' => function($data) {
                        if (Yii::$app->user->identity->checkRule('brands', 2)) {
                            return Html::a($data->id, ['view', 'id' => $data->id]);
                        }
                        return $data->id;
                    },
                ],
                'title',
                'city',
                'address',
                'phone',
                'federal_phone'
            ],
        ]) ?>
        </div>
    </div>
</div>
