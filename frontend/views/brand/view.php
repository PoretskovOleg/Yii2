<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Наши бренды', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="brand-view">
    <div class="box box-default">
        <div class="box-header with-border pull-right">
            <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этот бренд?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>

        <div class="box-body no-padding">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'title',
                    'slogan:ntext',
                    'city',
                    'address:ntext',
                    'phone',
                    'federal_phone',
                    'website',
                    'email:email',
                    'logo_filename' => [
                        'format' => 'html',
                        'label' => 'Логотип',
                        'value' => function($data)
                        {
                            if (empty($data->logo_filename))
                                return '';

                            return Html::img(
                                Yii::getAlias('@brands/logos/' . $data->logo_filename),
                                ['class' => 'img-responsive img-thumbnail']
                            );
                        },
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
