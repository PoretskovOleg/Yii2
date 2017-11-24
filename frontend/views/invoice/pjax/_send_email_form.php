<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>

<?php Pjax::begin(['id' => 'sendEmail', 'enablePushState' => false]); ?>
    <div class="box box-solid">

        <?php if (isset($success)): ?>
            <div class="box-header">
                <div class="alert alert-<?= $success ? 'success' : 'danger' ?>">
                    <?= $success ? 'Коммерческое предложение успешно отправлено' : 'Не удалось отправить коммерческое предложение' ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="box-body">
            <?php if (!isset($success) || !$success): ?>
                <?php $form = ActiveForm::begin([
                    'method' => 'POST',
                    'action' => 'send-email',
                    'successCssClass' => false,
                    'id' => 'good_search_form',
                    'options' => [
                        'data-pjax' => 1,
                        'enctype' => 'multipart/form-data',
                    ],
                ]); ?>

                <?= $email = $form->field($model, 'email')->input('email')->label('E-mail') ?>
                <?= $form->field($model, 'subject')->textInput()->label('Тема') ?>
                <?= $form->field($model, 'text')->textarea(['rows' => 5])->label('Текст') ?>

                <?= $form->field($model, 'invoice_filename')->textInput()->label('Имя файла') ?>

                <?= $form->field($model, 'attachment')->fileInput() ?>

                <?= Html::hiddenInput('invoice_id', $invoice_id) ?>

                <div class="form-group">
                    <?= Html::submitButton('Отправить', ['id' => 'send_email_button', 'class' => 'pull-right btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            <?php endif; ?>

            <?php if (isset($status)): ?>
                <?= Html::hiddenInput('change-status', $status, ['id' => 'change_status_from_email']) ?>
            <?php endif; ?>
        </div>

        <div id="send_email_overlay" class="overlay" style="display: none;">
            <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>
<?php Pjax::end(); ?>