<?php

use common\models\TechDep\TechDepPlanning;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use frontend\assets\TechDep\TechDepAsset;

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

$this->title = 'Планирование проекта № ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Реестр техотдела', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Редактировние проекта', 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Планирование проекта';
?>
<div class="tech-dep-project-planning">
<?php $form = ActiveForm::begin([
        'layout'=>'horizontal',
        'successCssClass' => false,
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <div class="box box-default">
        <div class="box-body">
            <div class="col-sm-12 no-padding" style="margin-bottom: 30px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Тип проекта</b> </div>
                <div class="col-sm-8"> <?=$model->typeProject->fullName; ?> </div>
            </div>
            
            <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Приоритет</b> </div>
                <div class="col-sm-8"> <?=$model->priorityProject->name; ?> </div>
            </div>

            <div>
                <?= $form->field($model, 'difficulty', $fieldOptions)->dropdownList(
                    $difficulty ); ?>
            </div>

            <div>
                <?php if (empty($model->responsible) || Yii::$app->user->identity->checkRule('tech-dep', 4 )) : ?>
                    <?= $form->field($model, 'responsible', $fieldOptions)->dropdownList(
                        $users, ['prompt' => ''] ); ?>
                <?php else : ?>
                    <?= $form->field($model, 'responsible', $fieldOptions)->dropdownList(
                        $users, ['disabled' => true, 'prompt' => '']); ?>
                    <div hidden>
                        <?= $form->field($model, 'responsible', $fieldOptions)->dropdownList(
                        $users, ['prompt' => '']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Что:</h3>
        </div>
        <div class="box-body">
            <div class="col-sm-12 no-padding" style="margin-bottom: 30px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>№ заказа</b> </div>
                <div class="col-sm-8"> <?=$model->orderNumber; ?> </div>
            </div>
            
            <div class="col-sm-12 no-padding" style="margin-bottom: 30px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Изделие</b> </div>
                <div class="col-sm-8"> <?=(!empty($model->goodId) ? $model->goodProject->goods_name : $model->goodName); ?> </div>
            </div>

            <div class="col-sm-12 no-padding" style="margin-bottom: 30px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Изменения</b> </div>
                <div class="col-sm-8"> <?=($model->changes ? $model->changes : 'Нет'); ?> </div>
            </div>

            <div class="col-sm-12 no-padding" style="margin-bottom: 30px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Примечание</b> </div>
                <div class="col-sm-8"> <?=($model->notice ? $model->notice : 'Нет'); ?> </div>
            </div>

            <div class="col-sm-12 no-padding" style="margin-bottom: 15px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Файлы</b> </div>
                <div class="col-sm-8">
                    <?php if (!empty($projectFiles)) : ?>
                        <?php foreach ($projectFiles as $file) : ?>
                            <span><a href="/files/tech-dep-files/<?=$file->name; ?>" target="_blank"><?=$file->name; ?></a>; </span>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <span>Нет прикрепленных файлов</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Когда:</h3>
        </div>
        <div class="box-body">
            <?= $form->field($model, 'timeStart', $fieldOptions)->widget(DatePicker::className(), [
                'language' => 'ru',
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'autoclose'=>true,
                    'todayHighlight' => true
            ]]); ?>

            <div class="col-sm-12 no-padding" style="margin: 15px -5px">
                <div class="col-sm-2" style="text-align: right; padding-right: 30px;"> <b>Дедлайн</b> </div>
                <div class="col-sm-8"> <?=$model->dedline; ?> </div>
            </div>

        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Исполнители:</h3>
        </div>
        <div class="box-body">
            <div class="col-sm-offset-3 col-sm-3">
                    <h4>Тип документа</h4>
            <?= Html::checkboxList('stages', TechDepPlanning::find()->where(['project' => $model->id])->select('stage')->column(), $stages, [
                    'id' => 'stagesProject',
                    'separator' => '<br>'
                ]); ?>
            </div>
            <div class="col-sm-3 contractor">
                <h4>Исполнитель</h4>
                <?php foreach ($stages as $id => $stage) : ?>
                    <?= Html::dropdownList('contractor_' . $id,
                        TechDepPlanning::find()->where(['project' => $model->id, 'stage' => $id])->select('contractor')->column(),
                        $users, ['prompt' => '']); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Расчет времени:</h3>
        </div>
        <div class="box-body">
            <table class="table table-striped table-bordered no-padding">
                <thead>
                    <tr>
                        <th></th>
                        <?php foreach ($stages as $id => $stage) : ?>
                            <th><?=$stage; ?></th>
                        <?php endforeach; ?>
                        <th>Общее время</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="stages">
                        <td></td>
                        <?php foreach ($stages as $id => $stage) : ?>
                            <td style="text-align: center"><?= Html::checkbox('times[]', TechDepPlanning::find()->where(['project' => $model->id, 'stage' => $id])->exists(), ['value' => $id])?></td>
                        <?php endforeach; ?>
                        <td></td>
                    </tr>
                    <tr>
                        <td><b>Из настроек:</b></td>
                        <?php foreach ($stages as $id => $stage) : ?>
                            <td></td>
                        <?php endforeach; ?>
                        <td></td>
                    </tr>
                    <tr class="dedline-time">
                        <td>Дедлайн (дни)</td>
                        <?php if (!empty($dedline)) : ?>
                            <?php foreach ($stages as $id => $stage) : ?>
                                <td><?= $dedline[$id]; ?></td>
                            <?php endforeach; ?>
                            <td class="total-dedline-setup"></td>
                        <?php else : ?>
                            <?php foreach ($stages as $id => $stage) : ?>
                                <td></td>
                            <?php endforeach; ?>
                            <td></td>
                        <?php endif; ?>
                    </tr>
                    <tr class="pure-time">
                        <td>Чистое (мин)</td>
                        <?php if (!empty($pure)) : ?>
                            <?php foreach ($stages as $id => $stage) : ?>
                                <td><?= $pure[$id]; ?></td>
                            <?php endforeach; ?>
                            <td class="total-pure-setup"></td>
                        <?php else : ?>
                            <?php foreach ($stages as $id => $stage) : ?>
                                <td></td>
                            <?php endforeach; ?>
                            <td></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td><b>Планируемое:</b></td>
                        <?php foreach ($stages as $id => $stage) : ?>
                            <td></td>
                        <?php endforeach; ?>
                        <td></td>
                    </tr>
                    <tr class="dedline-plan">
                        <td>Дедлайн (дни)</td>
                        <?php if (!empty($dedline)) : ?>
                            <?php foreach ($stages as $id => $stage) : ?>
                                <td> <input type="number" name="dedline_<?=$id?>"
                                    value="<?=(!empty($time = TechDepPlanning::find()->where(['project' => $model->id, 'stage' => $id])
                                        ->select('dedlineTime')->scalar()) ? $time : $dedline[$id]);?>"> </td>
                            <?php endforeach; ?>
                            <td class="total-dedline-plan"></td>
                        <?php else : ?>
                            <?php foreach ($stages as $id => $stage) : ?>
                                <td> <input type="number" name="dedline_<?=$id?>"
                                    value="<?=(!empty($time = TechDepPlanning::find()->where(['project' => $model->id, 'stage' => $id])
                                        ->select('dedlineTime')->scalar()) ? $time : '');?>"> </td>
                            <?php endforeach; ?>
                            <td class="total-dedline-plan"></td>
                        <?php endif; ?>
                    </tr>
                    <tr class="pure-plan">
                        <td>Чистое (мин)</td>
                        <?php if (!empty($pure)) : ?>
                            <?php foreach ($stages as $id => $stage) : ?>
                                <td> <input type="number" name="pure_<?=$id?>"
                                    value="<?=(!empty($time = TechDepPlanning::find()->where(['project' => $model->id, 'stage' => $id])
                                        ->select('pureTime')->scalar()) ? $time : $pure[$id]);?>"> </td>
                            <?php endforeach; ?>
                            <td class="total-pure-plan"></td>
                        <?php else : ?>
                            <?php foreach ($stages as $id => $stage) : ?>
                                <td> <input type="number" name="pure_<?=$id?>"
                                    value="<?=(!empty($time = TechDepPlanning::find()->where(['project' => $model->id, 'stage' => $id])
                                        ->select('pureTime')->scalar()) ? $time : '');?>"> </td>
                            <?php endforeach; ?>
                            <td class="total-pure-plan"></td>
                        <?php endif; ?>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>

    <div class="box box-default">
        <div class="box-body buttons">
            <div class = "col-sm-2 pull-right">
                <?= Html::a('Отмена', ['index'] ,['class' => 'btn btn-danger']); ?>
            </div>
            <div class = "col-sm-2 pull-right">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
    
    <div class="modal fade" id="difficultyModal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Почему Вы решили сменить сложность?</h4>
                </div>
                <div class="modal-body">
                    <p>Напишите Ваш комментарий</p>
                    <?=Html::textarea('difficulty_comment', '',
                        [
                            'id' => 'difficulty_comment',
                            'style' => 'width: 100%',
                            'rows' => '5'
                        ]);
                    ?>
                </div>
                <div class="modal-footer">
                    <?=Html::button('Отправить',
                        [
                            'type' => 'button',
                            'class' => 'btn btn-primary',
                            'id' => 'btn-difficulty'
                        ]);
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>