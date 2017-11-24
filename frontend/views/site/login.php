<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;

$fieldOptions = [
    'template' => '
        {label}
        <div class="col-md-10">{input}{hint}</div>
    ',
    'labelOptions' => [
        'class' => 'col-md-2 control-label',
    ],
];

?>

<div class="site-login">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="box box-info">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'successCssClass' => false,
                    'options' => ['class' => 'form-horizontal'],
                ]); ?>
                    <div class="box-body">
                        <?= $form->field($model, 'login', $fieldOptions)
                            ->textInput(['autofocus' => true])
                            ->label('Логин') ?>

                        <?= $form->field($model, 'password', $fieldOptions)
                            ->passwordInput()
                            ->label('Пароль') ?>

                        <?= $form->field($model, 'rememberMe', [
                            'template' => '
                                <div class="col-md-offset-2 col-md-10">
                                    <div class="checkbox">
                                        <label>
                                            {input} Запомнить
                                        </label>
                                    </div>
                                </div>
                            ',
                        ])
                            ->checkbox([], false)
                            ->label('Запомнить')
                        ?>
                    </div>

                    <div class="box-footer">
                        <?= Html::submitButton('Войти', [
                            'class' => 'btn btn-primary pull-right',
                            'name' => 'login-button'
                        ]) ?>
                    </div>
                <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
