<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use frontend\assets\TechDep\TechDepAsset;

TechDepAsset::register($this);

$this->title = 'Справка на материал';
$this->params['breadcrumbs'][] = ['label' => 'Реестр техотдела', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Проект', 'url' => ['update', 'id' => $project]];
$this->params['breadcrumbs'][] = ['label' => 'Этап', 'url' => ['stage', 'project' => $project, 'stage' => 4]];
$this->params['breadcrumbs'][] = 'Справка';

?>

<div class="tech-dep-project-reference">
    <div class="box box-default">
        <div class="box-header">
            <?php if ($canEdit) : ?>
                <?= Html::button('Добавить', ['class' => 'btn btn-primary pull-right', 'data-toggle' => 'modal', 'data-target' => '#add_material']) ?>
            <?php endif; ?>
        </div>

        <div class="box-body no-padding table-responsive" style="position: relative;">
            <table class="table table-striped table-bordered data-table table-condensed">
                <thead>
                <tr>
                    <th width="10%"></th>
                    <th width="5%">№ п/п</th>
                    <th width="10%">ID</th>
                    <th width="35%">Наименование</th>
                    <th width="10%">Ед. изм</th>
                    <th width="10%">Кол-во</th>
                    <th width="10%">Цена</th>
                    <th width="10%">Сумма</th>
                </tr>
                </thead>

                <tbody>
                    <?php if(!empty($materials)) : ?>
                        <?php foreach ($materials as $id => $material) : ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($canEdit) : ?>
                                        <span class="glyphicon glyphicon-arrow-up" style="margin-right: 5px"></span>
                                        <span class="glyphicon glyphicon-arrow-down" style="margin-right: 5px"></span>
                                        <span class="glyphicon glyphicon-pencil" style="margin-right: 5px"></span>
                                        <span class="glyphicon glyphicon-remove"></span>
                                    <?php endif; ?>
                                </td>
                                <td class="position"><?=$material['position']; ?></td>
                                <td class="id"><?=$id; ?></td>
                                <td class="name"><?=$material['name']; ?></td>
                                <td><?=$material['unit']; ?></td>
                                <td class="quantity"><?=$material['quantity']; ?></td>
                                <td class="price"><?=number_format($material['price'], 2, ',', ' '); ?></td>
                                <td class="sum"><?=number_format($material['quantity'] * $material['price'], 2, ',', ' '); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center"> Ничего не найдено </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="box-footer with-border">
            <div class="pull-right">
                <b><span>ВСЕГО: </span><span class="total"><?=number_format($total, 2, ',', ' ');?></span> руб.</b>
            </div>
        </div>

    </div>
    <?php if ($canEdit) : ?> 
        <div class="box box-default">
            <div class="box-footer">
                <?php $form = ActiveForm::begin(); ?>
                    <?= Html::submitButton('Сохранить', ['id' => 'submit_button', 'name' => 'submit_button', 'value' => 'submit_button', 'class' => 'pull-right btn btn-primary']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="modal fade" id="add_material" tabindex="false" role="dialog" aria-labelledby="add_good_modal_label" style="width: 100%;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="add_good_modal_label">Форма добавления материалов</h4>
                </div>
                <div class="modal-body">
                    <?= $this->render('/common/pjax/_good_search_form', ['goodSearchModel' => $goodSearchModel]) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
</div>
