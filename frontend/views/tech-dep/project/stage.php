<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use frontend\assets\TechDep\TechDepAsset;
use common\models\TechDep\TechDepPlanning;
use common\models\TechDep\TechDepTypeFileStage;

TechDepAsset::register($this);

$fieldOptions = [
    'template' => '
        <div class="col-sm-9 col-sm-offset-2">{error}</div>
        {label}
        <div class="col-sm-9">{input}{hint}</div>
    ',
    'labelOptions' => ['class' => 'control-label col-sm-2'],
    'hintOptions' => ['class' => 'help-block']
];

$this->title = '"' . $modelStage->stageProject->name . '" к проекту № ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Реестр техотдела', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Этап проекта';
?>

<div class="tech-dep-project-stage">
<?php $form = ActiveForm::begin([
        'layout'=>'horizontal',
        'successCssClass' => false,
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <div class="box box-default">
        <div class="box-body">
            <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Тип проекта</b> </div>
                <div class="col-sm-8"> <?=$model->typeProject->fullName; ?> </div>
            </div>
            
            <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Приоритет</b> </div>
                <div class="col-sm-8"> <?=$model->priorityProject->name; ?> </div>
            </div>

            <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Сложность</b> </div>
                <div class="col-sm-8"> <?=$model->difficulty; ?> </div>
            </div>

            <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Ответственный</b> </div>
                <div class="col-sm-8"> <?=$model->responsibleProject->shortName; ?> </div>
            </div>

            <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Исполнтель</b> </div>
                <div class="col-sm-8"> <?=$modelStage->contractorStage->shortName; ?> </div>
            </div>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Что:</h3>
        </div>
        <div class="box-body">
            <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>№ заказа</b> </div>
                <div class="col-sm-8"> <?=$model->orderNumber; ?> </div>
            </div>
            
            <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Изделие</b> </div>
                <div class="col-sm-8"> <?=(!empty($model->goodId) ? $model->goodProject->goods_name : $model->goodName); ?> </div>
            </div>

            <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Изменения</b> </div>
                <div class="col-sm-8"> <?=($model->changes ? $model->changes : 'Нет'); ?> </div>
            </div>

            <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Примечание</b> </div>
                <div class="col-sm-8"> <?=($model->notice ? $model->notice : 'Нет'); ?> </div>
            </div>
            <?php if ($modelStage->status != 1) : ?>
                <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                    <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Файлы проекта</b> </div>
                    <div class="col-sm-8">
                        <?php if (!empty($projectFiles)) : ?>
                            <?php foreach ($projectFiles as $file) : ?>
                                <span><a href="/files/tech-dep-files/<?=$file->name; ?>" target="_blank"><?=$file->name; ?></a> ; </span>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <span>Нет прикрепленных файлов</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($stagesFiles) && $modelStage->status != 1) : ?>
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Файлы этапов:</h3>
            </div>
            <div class="box-body no-padding">
                <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                    <?php foreach ($stagesFiles as $type => $files) : ?>
                        <div class="col-sm-12" style="margin-bottom: 15px;">
                            <div class="col-sm-3" style="text-align: right;"> <b><?=TechDepTypeFileStage::findOne($type)->name; ?></b> </div>
                            <div class="col-sm-9">
                                <?php foreach ($files as $file) : ?>
                                    <span><a href="/files/tech-dep-files/<?=$file->name; ?>" target="_blank"><?=$file->name; ?></a> 
                                    <?php if ($modelStage->contractor == Yii::$app->user->identity->user_id
                                            && $modelStage->status == 2 && $modelStage->stage == $file->stage) : ?>
                                    <span class="glyphicon glyphicon-trash" id="file_<?=$file->id?>"></span></span>
                                    <?php endif; ?>
                                    <span> ; </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($modelStage->status != 4 && $modelStage->status != 1) : ?>
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Загрузка файлов этапа:</h3>
            </div>
            <div class="box-body">
                <?php foreach ($typeFiles as $type) : ?>
                    <?= $form->field($modelStage, "typeFile_$type->id[]", $fieldOptions)->label($type->name)->fileInput(['multiple' => true])
                        ->hint($type->hint); ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Когда:</h3>
        </div>
        <div class="box-body">
            <div class="col-sm-12 no-padding" style="margin-bottom: 15px;">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Дата начала</b> </div>
                <div class="col-sm-8"> <?=$model->timeStart ? date('d.m.Y', $model->timeStart) : '-'; ?> </div>
            </div>

            <div class="col-sm-12 no-padding" style="margin-bottom: 15px;">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Время выполнения</b> </div>
                <div class="col-sm-8"> <?=$modelStage->pureTime; ?> мин.</div>
            </div>

            <div class="col-sm-12 no-padding" style="margin-bottom: 15px;">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Дедлайн</b> </div>
                <div class="col-sm-8"> <?=$model->timeStart ? date('d.m.Y', $model->timeStart + $modelStage->dedlineTime * 24*60*60) : '-'; ?> </div>
            </div>

        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Комментарии:</h3>
        </div>
        <div class="box-body">
            <?php if (!empty($modelComment)) : ?>
                <?php foreach ($modelComment as $comment) : ?>
                    <div class="col-sm-3"> <?=$comment->authorComment->shortName; ?> ( <?=date('d.m.Y H:i', $comment->createdAt)?> ): </div>
                    <div class="col-sm-9"> <?=$comment->comment ?> </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="box-footer with-border">
            <p><b>Ваш комментарий</b></p>
            <?=Html::textarea('commentStage', '',
                [
                    'id' => 'commentStage',
                    'style' => 'width: 100%'
                ]);
            ?>
        </div>
    </div>

    <div class="box box-default">
        <div class="box-footer buttons">
            <div class = "col-sm-2 pull-right">
                <?= Html::a('Отмена', ['index'] ,['class' => 'btn btn-danger']); ?>
            </div>
            <div class = "col-sm-2 pull-right">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'save', 'value' => 'save']); ?>
            </div>
            <?php if ($model->status == 7 && $modelStage->status == 1 && $modelStage->contractor == Yii::$app->user->identity->user_id &&
                    (
                        in_array($modelStage->stage, [1, 2, 3, 4]) ||
                        ($modelStage->stage == 5 && (!TechDepPlanning::find()->where(['and', ['project' => $modelStage->project], ['stage' => 3]])->exists() ||
                            (TechDepPlanning::find()->where(['and', ['project' => $modelStage->project], ['stage' => 3]])->exists() &&
                            TechDepPlanning::find()->where(['and', ['project' => $modelStage->project], ['stage' => 3]])->select('status')->scalar() == 4))) ||
                        ($modelStage->stage > 5 && (!TechDepPlanning::find()->where(['and', ['project' => $modelStage->project], ['stage' => 5]])->exists() ||
                            (TechDepPlanning::find()->where(['and', ['project' => $modelStage->project], ['stage' => 5]])->exists() &&
                            TechDepPlanning::find()->where(['and', ['project' => $modelStage->project], ['stage' => 5]])->select('status')->scalar() == 4)))
                    )
            ) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('Взять в работу', ['class' => 'btn btn-success', 'name' => 'in_work', 'value' => 'in_work']); ?>
                </div>
            <?php endif; ?>
            <?php if ($modelStage->status == 2) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('На утверждение', ['class' => 'btn btn-success', 'name' => 'on_approved', 'value' => 'on_approved']); ?>
                </div>
            <?php endif; ?>
            <?php if ($modelStage->status == 3 && $model->responsible == Yii::$app->user->identity->user_id) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('Утверждено', ['class' => 'btn btn-success', 'name' => 'approved', 'value' => 'approved']); ?>
                </div>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('На доработку', ['class' => 'btn btn-info', 'name' => 'reversion', 'value' => 'reversion']); ?>
                </div>
            <?php endif; ?>
            <?php if ($model->status == 7 && $modelStage->status == 4 && $model->responsible == Yii::$app->user->identity->user_id) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('На доработку', ['class' => 'btn btn-info', 'name' => 'reversion', 'value' => 'reversion']); ?>
                </div>
            <?php endif; ?>
            <?php if ($modelStage->stage == 4 && $modelStage->status > 1) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::a('Справка', ['reference', 'project' => $modelStage->project], ['class' => 'btn btn-info']); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">История:</h3>
        </div>
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
                            return $history->statusStage->name;
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