<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\assets\TechDep\TechDepAsset;

TechDepAsset::register($this);

$this->title = 'Таблица сложности';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tech-dep-difficulty no-padding">

    <div class="box box-default">
        <?php $form = ActiveForm::begin(['successCssClass' => false]) ?>
        <div class="box-body with-border no-padding">
            <table class="table table-bordered table-striped ">
                <thead>
                    <tr>
                        <th>Сложность</th>
                        <th>Время</th>
                        <th>ТЗ</th>
                        <th>Расчет 1</th>
                        <th>Расчет 2</th>
                        <th>Планирование</th>
                        <th>Расчет тех.</th>
                        <th>Справка на материал</th>
                        <th>Модель</th>
                        <th>Чертеж</th>
                        <th>Спецификация</th>
                        <th>Справка на инструмент</th>
                        <th>Тех. карта</th>
                        <th>Паспорт</th>
                        <th>Общее время</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model as $row) : ?>
                        <tr class="<?= (($row->id + 1)%3 == 0 ? 'calc-tr' : '') ?>">
                            <?php if (($row->id + 2)%3 == 0) : ?>
                                <td rowspan="3" class="difficulty"><?=$row->difficulty; ?></td>
                            <?php endif; ?>
                            <td class="time"><?=$row->stageName; ?> <?=($row->stageName == 'дедлайн' ? ' (дней)' : ' (минут)'); ?></td>
                            <td class="tz no-padding"> <input type="number" name='techTask[]' value="<?=($row->techTask ? $row->techTask : ''); ?>"></td>
                            <td class="culc_1 no-padding"> <input type="number" name='calc1[]' value="<?=($row->calc1 ? $row->calc1 : ''); ?>"></td>
                            <td class="culc_2 no-padding"> <input type="number" name='calc2[]' value="<?=($row->calc2 ? $row->calc2 : ''); ?>"> </td>
                            <td class="plan no-padding"> <input type="number" name='plan[]' value="<?=($row->plan ? $row->plan : ''); ?>"> </td>
                            <td class="culc_tech no-padding"> <input type="number" name='calcTech[]' value="<?=($row->calcTech ? $row->calcTech : ''); ?>"> </td>
                            <td class="materials no-padding"> <input type="number" name='materials[]' value="<?=($row->materials ? $row->materials : ''); ?>"> </td>
                            <td class="model no-padding"> <input type="number" name='model[]' value="<?=($row->model ? $row->model : ''); ?>"> </td>
                            <td class="draw no-padding"> <input type="number" name='draw[]' value="<?=($row->draw ? $row->draw : ''); ?>"> </td>
                            <td class="spec no-padding"> <input type="number" name='spec[]' value="<?=($row->spec ? $row->spec : ''); ?>"> </td>
                            <td class="tools no-padding"> <input type="number" name='tools[]' value="<?=($row->tools ? $row->tools : ''); ?>"> </td>
                            <td class="tech_map no-padding"> <input type="number" name='techMap[]' value="<?=($row->techMap ? $row->techMap : ''); ?>"> </td>
                            <td class="passport no-padding"> <input type="number" name='passport[]' value="<?=($row->passport ? $row->passport : ''); ?>"> </td>
                            <td class="project no-padding"> <input type="number" name='project[]' value="<?=($row->project ? $row->project : ''); ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="box-footer with-border">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
