<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\assets\TechDep\TechDepAsset;

TechDepAsset::register($this);

$this->title = 'Редактирование проекта № ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Реестр техотдела', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактирование проекта';
?>
<div class="tech-dep-project-update">

    <?= $this->render('_form', [
        'model' => $model,
        'difficulty' => $difficulty,
        'projectFiles' => $projectFiles,
        'isStageInWork' => $isStageInWork,
        'stagesFiles' => $stagesFiles,
        'modelComment' => $modelComment
    ]) ?>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">История:</h3>
        </div>
        <div class="box-body no-padding">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'layout'=>'<div class="box-body no-padding table-responsive">{items}</div>
                                   <div class="box-footer">{pager}</div>',
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered no-padding'
                ],
                'columns' => [
                    'status' => [
                        'label' => 'Статус',
                        'value' => function($history)
                        {
                            return $history->statusProject->name;
                        }
                    ],
                    'createdAt' => [
                        'label' => 'Дата и время',
                        'value' => function($history)
                        {
                            return date('d.m.Y H:i', $history->createdAt);
                        }
                    ],
                    'author' => [
                        'label' => 'Автор',
                        'value' => function($history)
                        {
                            return $history->authorHistory->shortName;
                        }
                    ],
                    'comment' => [
                        'label' => 'Комментарий',
                        'value' => function($history)
                        {
                            return $history->comment;
                        }
                    ]
                ]
            ]); ?>
        </div>
    </div>

</div>
