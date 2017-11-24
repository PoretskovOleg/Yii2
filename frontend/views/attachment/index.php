<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Прикрепляемые файлы';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="attachment-index">
    <div class="box box-default">
        <div class="box-header with-border">
            <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-primary pull-right']) ?>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => '<div class="box-body no-padding table-responsive">{items}</div><div class="box-footer">{pager}</div>',
            'columns' => [
                'id' => [
                    'label' => '№',
                    'format' => 'html',
                    'value' => function($data) {
                        return Html::a($data->id, ['update', 'id' => $data->id]);
                    },
                ],
                'name',
                'filename' => [
                    'label' => 'Файл',
                    'format' => 'html',
                    'value' => function($data) {
                        return Html::a($data->filename, Yii::getAlias('@attachments/' . $data->filename));
                    },
                ],
            ],
        ]); ?>
    </div>
</div>
