<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use common\models\TechDep\TechDepTypeProject;
use common\models\TechDep\TechDepPriorityProject;
use common\models\TechDep\TechDepPlanning;
use common\models\TechDep\TechDepTypeFileStage;

$fieldOptions = [
    'template' => '
        <div class="col-sm-9 col-sm-offset-2">{error}</div>
        {label}
        <div class="col-sm-9">{input}{hint}</div>',
    'labelOptions' => ['class' => 'control-label col-sm-2'],
    'hintOptions' => ['class' => 'help-block']
];

?>

<div class="tech-dep-project-form">

    <?php $form = ActiveForm::begin([
        'layout'=>'horizontal',
        'successCssClass' => false,
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <div class="box box-default">
        <div class="box-body">
            <?= $form->field($model, 'type', $fieldOptions)->dropdownList(
                TechDepTypeProject::find()->select(['fullName', 'id'])->indexBy('id')->column(),
                ['prompt' => '']
            ); ?>

            <?php if ($model->isNewRecord || Yii::$app->user->identity->checkRule('tech-dep', 3)) : ?>
                <?= $form->field($model, 'difficulty', $fieldOptions)->dropdownList(
                    $difficulty, ['prompt' => '']); ?>
            <?php else : ?>
                <?= $form->field($model, 'difficulty', $fieldOptions)->dropdownList(
                    $difficulty, ['disabled' => true, 'prompt' => '']); ?>
                <div hidden>
                    <?= $form->field($model, 'difficulty', $fieldOptions)->dropdownList(
                    $difficulty, ['prompt' => '']); ?>
                </div>
            <?php endif; ?>

            <?php if (Yii::$app->user->identity->checkRule('tech-dep', 3)) : ?>
                <?= $form->field($model, 'priority', $fieldOptions)->dropdownList(
                TechDepPriorityProject::find()->select(['name', 'id'])->indexBy('id')->column(),
                ['options' => $model->priority ? array() : ['3' => ['selected' => true]]]); ?>
            <?php else : ?>
                <?= $form->field($model, 'priority', $fieldOptions)->dropdownList(
                    TechDepPriorityProject::find()->select(['name', 'id'])->indexBy('id')->column(),
                    ['disabled' => true, 'options' => $model->priority ? array() : ['3' => ['selected' => true]]]
                )->label('Приоритет')->hint('По умолчанию "обычный". Если требуется повысить приоритет, отправляйте письмо на pdo1@azavod.ru'); ?>
                <div hidden>
                    <?= $form->field($model, 'priority', $fieldOptions)->dropdownList(
                    TechDepPriorityProject::find()->select(['name', 'id'])->indexBy('id')->column(),
                    ['options' => $model->priority ? array() : ['3' => ['selected' => true]]]); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Что:</h3>
        </div>
        <div class="box-body">
            <?= $form->field($model, 'orderNumber', ['template' =>
                    '<div class="col-sm-9 col-sm-offset-2">{error}</div>
                    {label}
                    <div class="col-sm-9 input-group" style="padding: 0 15px;">
                        {input}
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-success btn-flat">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>{hint}
                    </div>',
                'inputOptions' => ['class' => 'form-control', 'placeholder' => '№ счета, например, T-8815'],
                'labelOptions' => ['class' => 'control-label col-sm-2']
                ])->textInput(); ?>

            <div class="goodId" <?=((!empty($model->type) && $model->type == 4) ? 'hidden' : '' ); ?>>
                <?php if ($model->isNewRecord) : ?>
                    <?= $form->field($model, 'goodId', $fieldOptions)->dropdownList(
                    array(), ['prompt' => '']); ?>
                <?php else : ?>
                    <?= $form->field($model, 'goodId', $fieldOptions)->dropdownList(
                    (!empty($model->goodId) ? [$model->goodId => $model->goodProject->goods_name] : array()), ['options' =>[$model->goodId => ['selected' => true]]]); ?>
                <?php endif; ?>
            </div>

            <div class="goodName" <?=($model->type != 4 ? 'hidden' : '' ); ?>>
                <?= $form->field($model, 'goodName', $fieldOptions)->textInput(); ?>
            </div>

            <div class="changes" <?=(($model->isNewRecord || $model->type != 2) ? 'hidden' : ''); ?>>
                <?= $form->field($model, 'changes', $fieldOptions)->textarea()
                ->hint('Опишите изменения от типового изделия'); ?>
            </div>

            <?php if (!$model->isNewRecord) : ?>
                <div class="col-sm-12 no-padding">
                    <div class="col-sm-2" style="text-align: right;"><b>Файлы проекта: </b></div>
                    <div class="col-sm-8">
                        <?php if (!empty($projectFiles)) : ?>
                            <?php foreach ($projectFiles as $file) : ?>
                                <span> 
                                    <a href="/files/tech-dep-files/<?=$file->name; ?>" target="_blank"><?=$file->name; ?></a>
                                    <?php if ($model->authorId == Yii::$app->user->identity->user_id && $model->status == 1 ||
                                            Yii::$app->user->identity->checkRule('tech-dep', 6)) : ?>
                                        <span class="glyphicon glyphicon-trash" id="file_<?=$file->id?>"></span>
                                    <?php endif; ?>
                                    <span> ; </span>
                                </span>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <span>Нет прикрепленных файлов</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?= $form->field($model, 'projectFiles[]', $fieldOptions)->fileInput(['multiple' => true])
                ->hint('Прикрепите ТЗ в PDF, можете прикрепить доп файлы (картинки, чертежи и т.д.). Не более 5 файлов и не более 2 МБ'); ?>
        </div>
    </div>

    <?php if (!empty($stagesFiles)) : ?>
        <div class="box box-default box-solid">
            <div class="box-header with-border">
                    <h3 class="box-title">Файлы этапов:</h3>
                </div>
            <div class="box-body no-padding">
                <?php foreach ($stagesFiles as $type => $files) : ?>
                    <div class="col-sm-12" style="margin-bottom: 15px;">
                        <div class="col-sm-3" style="text-align: right;"> <b><?=TechDepTypeFileStage::findOne($type)->name; ?></b> </div>
                        <div class="col-sm-9">
                            <?php foreach ($files as $file) : ?>
                                <span><a href="/files/tech-dep-files/<?=$file->name; ?>" target="_blank"><?=$file->name; ?></a> 
                                <?php if (Yii::$app->user->identity->checkRule('tech-dep', 6)) : ?>
                                    <span class="glyphicon glyphicon-trash" id="file_<?=$file->id?>"></span></span>
                                <?php endif; ?>
                                <span> ; </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
                <h3 class="box-title">Примечание:</h3>
            </div>
        <div class="box-body">
            <?= $form->field($model, 'notice', $fieldOptions)->textarea()
                ->hint('Напишите важные примечания: цвет, материал и т.д.'); ?>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Когда:</h3>
        </div>
        <div class="box-body">
            <?= $form->field($model, 'dedline', $fieldOptions)->widget(DatePicker::className(), [
                'language' => 'ru',
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'autoclose'=>true,
                    'todayHighlight' => true
            ]]); ?>
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
            <?=Html::textarea('commentProject', '',
                [
                    'id' => 'commentProject',
                    'style' => 'width: 100%'
                ]);
            ?>
        </div>
    </div>

     <div class="box box-default">
        <div class="box-footer buttons">
            <?php if (!$model->isNewRecord && Yii::$app->user->identity->checkRule('tech-dep', 8)) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('Удалить', ['class' => 'btn bg-maroon', 'name' => 'delete', 'formaction' => './delete?id='.$model->id]); ?>
                </div>
            <?php endif; ?>
            <div class = "col-sm-2 pull-right">
                <?= Html::a('Отмена', ['index'] ,['class' => 'btn btn-danger']); ?>
            </div>
            <?php if ($model->isNewRecord) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('Создать', ['class' => 'btn btn-primary']); ?>
                </div>
            <?php endif; ?>
            <?php if (!$model->isNewRecord && (($model->status <= 5 && $model->authorId == Yii::$app->user->identity->user_id) ||
                                  Yii::$app->user->identity->checkRule('tech-dep', 3))) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>
                </div>
            <?php endif; ?>
            <?php if (((!$model->isNewRecord && !$isStageInWork && in_array($model->status, [6, 7])) || in_array($model->status, [2, 3, 5]) ) 
                            && Yii::$app->user->identity->checkRule('tech-dep', 2)) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::a('Планирование', ['planning', 'id' => $model->id], ['class' => 'btn btn-success']); ?>
                </div>
            <?php endif; ?>
            <?php if ($model->status == 1 && $model->type == 4 && $model->authorId == Yii::$app->user->identity->user_id) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('Расчет Д1', ['class' => 'btn btn-success', 'name' => 'calc_1', 'value' => 'calc_1']); ?>
                </div>
            <?php endif; ?>
            <?php if ($model->status == 6 && $model->responsible == Yii::$app->user->identity->user_id) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('Взять в работу', ['class' => 'btn btn-success', 'name' => 'in_work', 'value' => 'in_work']); ?>
                </div>
            <?php endif; ?>
            <?php if ($model->status == 7 && $model->responsible == Yii::$app->user->identity->user_id) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('На утверждение', ['class' => 'btn btn-success', 'name' => 'on_approved', 'value' => 'on_approved']); ?>
                </div>
            <?php endif; ?>
            <?php if ($model->status == 8 && Yii::$app->user->identity->checkRule('tech-dep', 5) &&
                ((TechDepPlanning::find()->where(['and', ['project' => $model->id], ['in', 'stage', [1, 2] ]])->count() == 2 &&
                TechDepPlanning::find()->where(['and', ['project' => $model->id], ['status' => 4]])->count() != 2) ||
                TechDepPlanning::find()->where(['and', ['project' => $model->id], ['in', 'stage', [1, 2] ]])->count() != 2)) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('Утверждено', ['class' => 'btn btn-success', 'name' => 'approved', 'value' => 'approved']); ?>
                </div>
            <?php endif; ?>
            <?php if ($model->status == 8 && Yii::$app->user->identity->checkRule('tech-dep', 5)) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('На доработку', ['class' => 'btn btn-info', 'name' => 'reversion', 'value' => 'reversion']); ?>
                </div>
            <?php endif; ?>
            <?php if ($model->status == 9 && $model->type == 4 && $model->authorId == Yii::$app->user->identity->user_id) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('Расчет Д2', ['class' => 'btn btn-success', 'name' => 'calc_2', 'value' => 'calc_2']); ?>
                </div>
            <?php endif; ?>
            <?php if ($model->status == 8 && $model->type != 4 &&
                TechDepPlanning::find()->where(['and', ['project' => $model->id], ['in', 'stage', [1, 2] ]])->count() == 2 &&
                TechDepPlanning::find()->where(['and', ['project' => $model->id], ['status' => 4]])->count() == 2 &&
                Yii::$app->user->identity->checkRule('tech-dep', 2)) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::a('Планирование', ['planning', 'id' => $model->id], ['class' => 'btn btn-success']); ?>
                </div>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('К менеджеру', ['class' => 'btn btn-success', 'name' => 'manager', 'value' => 'manager']); ?>
                </div>
            <?php endif; ?>
            <?php if (($model->status == 4 || ($model->status == 1 && $model->type != 4)) && $model->authorId == Yii::$app->user->identity->user_id) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('В работу', ['class' => 'btn btn-success', 'name' => 'in_plan', 'value' => 'in_plan']); ?>
                </div>
            <?php endif; ?>
            <?php if ($model->status == 9 && $model->type != 4 && !$model->archive && Yii::$app->user->identity->checkRule('tech-dep', 3)) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::submitButton('В архив', ['class' => 'btn btn-success', 'name' => 'archive', 'value' => 'archive']); ?>
                </div>
            <?php endif; ?>
            <?php if (TechDepPlanning::find()->select('status')->where(['and', ['project' => $model->id], ['stage' => 4]])->scalar() == 4) : ?>
                <div class = "col-sm-2 pull-right">
                    <?= Html::a('Справка', ['reference', 'project' => $model->id], ['class' => 'btn btn-info']); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
