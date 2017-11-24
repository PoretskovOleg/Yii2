<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;

$fieldOptions = [
    'template' => '
            <div class="col-md-12">
                {label}
                {input}{hint}
            </div>
    ',
    'hintOptions' => [
        'class' => 'help-block',
    ],
    'labelOptions' => [
        'style' => 'margin-right: 10px;'
    ],
];

?>

<?php Pjax::begin(['id' => 'goods', 'enablePushState' => false]); ?>
<div class="box box-default">
    <div class="box-header">
        <?php $form = ActiveForm::begin([
            'method' => 'POST',
            'action' => 'search-goods',
            'successCssClass' => false,
            'id' => 'good_search_form',
            'options' => [
                'data-pjax' => 1,
                'class' => 'form-inline',
            ],
        ]); ?>

        <?= $form->field($goodSearchModel, 'id', $fieldOptions)
            ->textInput(['class' => 'form-control good-search-input'])
        ?>
        <?= $form->field($goodSearchModel, 'name', $fieldOptions)
            ->textInput(['class' => 'form-control good-search-input'])
        ?>

        <div class="form-group">
            <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
        </div>

        <?= $form->field($goodSearchModel, 'page')->hiddenInput(['id' => 'good_search_selected_page', 'class' => ''])->label(false) ?>
        <?php ActiveForm::end(); ?>
    </div>

    <?php if (isset($goods) && !empty($goods)): ?>
        <div class="box-body no-padding table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Арт.</th>
                        <th>Номенклатура</th>
                        <th>Склад ЧХВ</th>
                        <th>Склад АВМ</th>
                        <th>Цена баз.</th>
                        <th width="5%">Количество</th>
                        <th>Выбрать</th>
                    </tr>
                </thead>

                <?php foreach ($goods as $good): ?>
                    <tr class="good">
                        <td class="good-id">
                            <?= $good->goods_id ?>
                        </td>

                        <td class="good-name">
                            <?= $good->goods_name ?>
                        </td>

                        <td class="good-ch-stock-balance">
                            <?= $good->freeAmounts['che'] ?> шт
                        </td>

                        <td class="good-avm-stock-balance">
                            <?= $good->freeAmounts['avm'] ?> шт
                        </td>

                        <td class="good-base-price">
                            <?php $base_price = $good->price + ($good->price / 100 * $good->margin) ?>
                            <?php $base_price = $base_price + ($base_price / 100 * $good->extra_charge) ?>
                            <?= sprintf('%01.2f', $base_price) ?>
                        </td>

                        <td class="good-count">
                            <?= Html::input('number', null, 1, ['id' => 'good-count-input-'. $good->goods_id, 'class' => 'form-control', 'min' => 1]) ?>
                        </td>

                        <td class="good-select">
                            <?= Html::button('Выбрать', [
                                'class' => 'btn btn-primary good-select-button',
                                'data-good-id' => $good->goods_id,
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="box-footer">
            <?= LinkPager::widget([
                'pagination' => $pages,
                'firstPageLabel' => '<i class="fa fa-long-arrow-left"></i>',
                'lastPageLabel' => '<i class="fa fa-long-arrow-right"></i>',
                'maxButtonCount' => 20,
                'linkOptions' => [
                    'class' => 'goods-pagination-link',
                ],
            ]) ?>
        </div>
    <?php else: ?>
        <div class="box-body">
            <div class="col-md-12 text-center">
                Ничего не найдено
            </div>
        </div>
    <?php endif; ?>
</div>
<?php Pjax::end(); ?>