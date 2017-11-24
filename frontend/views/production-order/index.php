<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use frontend\assets\Production\ProductionAsset;
use common\models\Production\ProductionPrepareOrder;

ProductionAsset::register($this);

$this->title = 'Реестр заказ-нарядов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="production-order-index">
    <?php if (Yii::$app->user->identity->checkRule('production-order', 2)) : ?>
        <div class="box box-info">
            <div class="box-body">
                <?= Html::a('Создать заказ', ['create'], ['class' => 'btn btn-primary pull-right']) ?>
            </div>
        </div>
    <?php endif;?>

    <div class="box box-info box-solid">

        <div class="box-header with-border">
            <h3 class="box-title">Поиск</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="box-body no-padding">
            <?=$this->render('_search',[
                'model' => $searchModel,
                'priority' => $priority,
                'target' => $target,
                'theme' => $theme,
                'typeGood' => $typeGood,
                'typeOrder' => $typeOrder,
                'responsible' => $responsible,
                'otk' => $otk,
                'status' => $status,
                'stage' => $stage,
            ]); ?>
        </div>
    </div>
        
    <div class="box box-info">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout'=>'<div class="box-body no-padding table-responsive">{items}</div>
                       <div class="box-footer">{pager}</div>',
            'tableOptions' => [
                'class' => 'table table-striped table-bordered no-padding'
            ],
            'columns' => [
                'sequence' => [
                    'label' => 'П',
                    'format' => 'raw',
                    'contentOptions'   =>   ['style' => 'padding: 3px;'],
                    'headerOptions' => ['width' => '1%'],
                    'value' => function($order) use ($sequence) {
                        if ($order->status < 4)
                            if (Yii::$app->user->identity->checkRule('production-order', 7)) {
                                if (empty($order->sequence)) $sequence[count($sequence) + 1] = count($sequence) + 1;
                                $value = Html::dropDownList('sequence', $order->sequence, $sequence, ['prompt' => '']);
                            }
                            else $value = !empty($order->sequence) ? $order->sequence : '';
                        else
                            $value = '';
                        return $value;
                    }
                ],
                'typeGood' => [
                    'label' => 'Тип',
                    'format' => 'raw',
                    'contentOptions'   =>   ['style' => 'padding: 3px;'],
                    'headerOptions' => ['width' => '3%'],
                    'value' => function($order)
                    {
                        $value = '';
                        if ($order->priority == 1) {
                            $value .= '<div><div class="text-center sign" data-title="ОГОНЬ!!!"><img src="/images/driver/fire.png" alt="фото"></div></div>';
                        }elseif ($order->priority == 2) {
                            $value .= '<div><div class="text-center sign" data-title="Важно!"><img src="/images/driver/warning.png" alt="фото"></div></div>';
                        }
                        $value .= '<div class="text-center"><b>' . 
                            (!empty($order->good) ?
                                (!empty($order->goodOrder->goods_name) ?
                                    (empty($order->goodOrder->is_service) ? 'Т' : 'У') : 'У') : ($order->target == 3 ? 'У' : '')) . '</b></div>';
                        return $value;
                    }
                ],
                'id' => [
                    'label' => 'ID',
                    'format' => 'html',
                    'contentOptions'   =>   ['style' => 'padding: 3px;'],
                    'headerOptions' => ['width' => '7%'],
                    'value' => function($order)
                    {
                        
                        $value = '<div><a href="./update?id='.$order->id.'">З/Н: <b>' . (empty($order->number) ? $order->id : $order->number) . '</b></a></div>';

                        $value .= '<div>от ' . date('d.m.Y', $order->createdAt) . '</div>';

                        if (!empty($order->order))
                            $value .= '<div>Счет: ' . $order->order . ' <span>' . ($order->typeGood == 2 ? "<b>И</b>" : "") . '</span></div>';
                        else $value .= '<div>' . ($order->typeGood == 2 ? "<b>И</b>" : "") . '</div>';

                        return $value;
                    }
                ],
                'nameGood' => [
                    'label' => 'Наименование товара',
                    'format' => 'html',
                    'contentOptions'   =>   ['style' => 'padding: 3px;'],
                    'value' => function($order)
                    {
                        return '<div>' . (empty($order->good) ?
                            '' : ($order->good . ': ')) . (empty($order->good) ?
                                $order->nameGood : (empty($order->goodOrder->name) ?
                                    $order->goodOrder->goods_name : $order->goodOrder->name)) . '</div>' .
                            '<div><b>Ответственный:</b> ' . ( empty($order->responsible) ? ' - ' : $order->responsibleOrder->shortName) . '</div>';
                    }
                ],
                'countOrder' => [
                    'label' => 'Кол-во',
                    'format' => 'html',
                    'contentOptions'   =>   ['style' => 'padding: 3px;'],
                    'headerOptions' => ['width' => '5%'],
                    'value' => function($order)
                    {
                        return '<div class="text-center">' . 
                            (empty($order->countOrder) ? $order->countStock : $order->countOrder) . 
                            ' шт</div>';
                    }
                ],
                'notice' => [
                    'label' => 'Примечания, комментарии',
                    'format' => 'raw',
                    'contentOptions'   =>   ['style' => 'padding: 3px;'],
                    'headerOptions' => ['width' => '10%'],
                    'value' => function($order)
                    {
                        return '<div>' . $order->notice . '</div>' .
                            (!empty($order->notice) && !empty($order->commentsOrder[0]) ? '<hr style="margin: 5px 0">' : '') .
                            '<div>' . (!empty($order->commentsOrder[0]) ? $order->commentsOrder[0]->comment : '') . '</div>' .
                            '<div class="see-comment" data-id="'.$order->id.'">+Комментарии</div>';
                    }
                ],
                'dedline' => [
                    'label' => 'Даты',
                    'format' => 'html',
                    'contentOptions'   =>   ['style' => 'padding: 3px;'],
                    'headerOptions' => ['width' => '8%'],
                    'value' => function($order)
                    {
                        if ($order->status < 6 && !empty($order->dedline)) {
                            $days = floor(($order->dedline - strtotime('now')) / (24*60*60));
                            return '<div>Дедлайн произв: ' . date('d.m.Y', $order->dedline) . '</div>' .
                                '<div class="' . ($days >= 0 ? 'green' : 'red') . '">' . ($days == 0 ? 'сегодня' : $days . ' дн') . '</div>';
                        } elseif ($order->status == 7) {
                            $days = floor(($order->dedline - $order->finishedAt) / (24*60*60));
                            return '<div><b>Завершено:</b> ' . date('d.m.Y H:i', $order->finishedAt) . '</div>' .
                                    '<div><b>Отклонение:</b> <div class="' . ($days >= 0 ? 'green' : 'red') . '">' . $days . ' дн</div></div>';
                        }
                        else return '';
                    }
                ],
                'prepare' => [
                    'label' => 'Подготовка',
                    'format' => 'raw',
                    'contentOptions'   =>   ['style' => 'padding: 3px;'],
                    'headerOptions' => ['width' => '7%'],
                    'value' => function($order)
                    {
                        $stages = ProductionPrepareOrder::findAll(['order' => $order->id]);
                        $value = '<div class="text-center">';
                        foreach ($stages as $key => $stage) {
                            $value .= 
                                '<button ' .
                                    'data-id="' . (
                                        ($stage->isPrepare == 0 && $order->status == 2 && $order->responsible == Yii::$app->user->identity->user_id) ? $stage->id : 0
                                    ) .
                                    '" data-name="' . $stage->stagePrepare->name .
                                    '" class="prepare btn btn-' . ($stage->isPrepare == 0 ? 'danger' : 'success') .
                                    '" style="padding: 3px 10px; margin: 2px 5px;">' .
                                        $stage->stagePrepare->shortName .
                                '</button>';
                            if ((($key + 1) % 2 == 0) && ($key != count($stages) - 1) ) $value .= '</div><div class="text-center">';
                        }
                        $value .= '</div>';
                        return $value;
                    }
                ],
                'status' => [
                    'label' => 'Статус',
                    'format' => 'raw',
                    'contentOptions'   =>   ['style' => 'padding: 3px; vertical-align: middle;'],
                    'headerOptions' => ['width' => '8%'],
                    'value' => function($order)
                    {
                        return 
                            '<div class="status ' . $order->statusOrder->color . '" style="width: 98%; padding: 10px 0;">' .
                                    $order->statusOrder->name .
                            '</div>';
                    }
                ],
                'stage' => [
                    'label' => 'Этап',
                    'format' => 'html',
                    'contentOptions'   =>   ['style' => 'padding: 3px; vertical-align: middle;'],
                    'headerOptions' => ['width' => '7%'],
                    'value' => function($order)
                    {
                        return empty($order->stage) ? '' : 
                            '<div class="' . $order->stageOrder->color . '" style="width: 98%; padding: 10px 0;">' . $order->stageOrder->name . '</div>';
                    }
                ],
                'otk' => [
                    'label' => 'ОТК',
                    'format' => 'html',
                    'contentOptions'   =>   ['class' => 'text-center', 'style' => 'padding: 3px; vertical-align: middle;'],
                    'headerOptions' => ['width' => '10%'],
                    'value' => function($order)
                    {
                        return empty($order->otk) ? '' : '<div><b>' . $order->otkOrder->shortName . '</b></div>';
                    }
                ]
            ],
        ]); ?>
    </div>

    <div class="modal fade" id="commentOrder" tabindex="-1" role="dialog" aria-labelledby="commentOrderLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="commentOrderLabel">Комментарии к заказ-наряду</h4>
                </div>
                <?php $formComment = ActiveForm::begin(); ?>
                <div class="modal-body">
                    <div class="box box-solid">
                        <div class="box-body">
                            <table class="table table-striped">
                                <thead>
                                    <th width="20%">Дата и время</th>
                                    <th width="20%">Автор</th>
                                    <th width="60%">Комментарий</th>
                                </thead>

                                <tbody id="view_comments_tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="box box-solid">
                        <div class="box-body">
                            <input type="text" name="id" hidden>
                            <textarea name="comment" style="width: 100%"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?= Html::submitButton('Добавить',
                        ['name' => 'addComment', 'value' => 'addComment', 'class' => 'btn btn-success',]); ?>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Закрыть</button>
                </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPrepareOrder" tabindex="-1" role="dialog" aria-labelledby="prepareOrderLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title" id="prepareOrderLabel"></h4>
                </div>
                <div class="modal-body text-center">
                    <button type="button" class="btn btn-success" name="approved" style="width: 175px; margin-right: 20px;">Подтверждаю</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" style="width: 175px">Отмена</button>
                </div>
            </div>
        </div>
    </div>
</div>
