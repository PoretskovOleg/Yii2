<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\bootstrap\ActiveForm;
    use kartik\date\DatePicker;
    use common\models\Driver\DriverTypeTrip;
    use common\models\Driver\DriverAddress;
    use common\models\Driver\DriverPriority;
    use frontend\assets\Driver\DriverAsset;

    DriverAsset::register($this);
    $this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU');

    $fieldOptions = [
        'template' => '
            <div class="col-sm-9 col-sm-offset-2">{error}</div>
            {label}
            <div class="col-sm-9">{input}{hint}</div>
        ',
        'labelOptions' => ['class' => 'control-label col-sm-2'],
        'hintOptions' => ['class' => 'help-block']
    ];

    $this->title = !$edit ? 'Создать поездку' : 'Редактирование поездки №' . $model->id;
?>

<div class="create_trip">

    <?php $form = ActiveForm::begin(['layout'=>'horizontal', 'successCssClass' => false]) ?>
    
    <div class="box box-default">
        <div class="box-body">
            <div <?= $edit ? 'hidden' : ''; ?> >
                <?= $form->field($model, 'typeOfTrip', $fieldOptions)->dropdownList(
                    DriverTypeTrip::find()->select(['name', 'id'])->indexBy('id')->column(),
                    ['prompt' => '']
                ); ?>
            </div>
            <?php if (!$edit) : ?>
                <?php if (Yii::$app->user->identity->checkRule('driver', 5)) : ?>
                    <?= $form->field($model, 'priority', $fieldOptions)->dropdownList(
                        DriverPriority::find()->select(['name', 'id'])->indexBy('id')->column(),
                        ['options' =>['3' => ['selected' => true]]]); ?>
                <?php else : ?>
                    <?= $form->field($model, 'priorityStart', $fieldOptions)->dropdownList(
                        DriverPriority::find()->select(['name', 'id'])->indexBy('id')->column(),
                        ['disabled' => true, 'options' =>['3' => ['selected' => true]]]
                    )->label('Приоритет')->hint('По умолчанию "обычный". Если требуется повысить приоритет, отправляйте письмо на pdo1@azavod.ru'); ?>
                    <div hidden>
                        <?= $form->field($model, 'priority', $fieldOptions)->dropdownList(
                        DriverPriority::find()->select(['name', 'id'])->indexBy('id')->column(),
                        ['options' =>['3' => ['selected' => true]]]); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($edit) { ?>
                    <div id="type_trip" hidden><?=$model->typeOfTrip?></div>
                    <div><span class="header_trip" >Статус: </span>
                        <span id="status" class="<?= $model->statusTrip->color?>"> <?= $model->statusTrip->name; ?></span>
                    </div>
                    <div><span class="header_trip">Тип поездки:</span> <b><?= $model->typeTrip->name; ?></b></div>
                    <div><span class="header_trip">Автор:</span> <b><?php $user = $model->author;
                        echo $user->last_name . ' ' . mb_substr($user->first_name, 0, 1) . '.' . mb_substr($user->patronymic, 0, 1) . '.' ?></b>
                    </div>
                    <div><span class="header_trip">Дата и время создания:</span> <b><?= date('d.m.y H:i', $model->createdAt); ?></b></div>

                    <?php if (in_array($model->status, [1, 2, 3, 4]) && Yii::$app->user->identity->checkRule('driver', 5)) { ?>
                        <?= $form->field($model, 'priority', $fieldOptions)->dropdownList(
                            DriverPriority::find()->select(['name', 'id'])->indexBy('id')->column(),
                            ['prompt' => '']
                        ); ?>
                    <?php } else { ?>
                        <div hidden>
                            <?= $form->field($model, 'priority', $fieldOptions)->dropdownList(
                                DriverPriority::find()->select(['name', 'id'])->indexBy('id')->column(),
                                ['prompt' => '']
                            ); ?>
                        </div>
                        <div><span class="header_trip">Приориет:</span> <b> <?= DriverPriority::findOne($model->priority)->name ?> </b></div>
                    <?php } ?>
            <?php } ?>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
                <h3 class="box-title">Примечание:</h3>
            </div>
        <div class="box-body">
            <?= $form->field($model, 'notice', $fieldOptions)->textarea()
                ->hint('Укажите время работы, заказ пропуска, платный въезд, требования разгрузки-загрузки. Важная информация для путевого листа.'); ?>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Что:</h3>
        </div>
        <div class="box-body">
            <div class="order_id" <?= (!$edit || $model->typeOfTrip == 4) ? 'hidden' : '' ?> >
                <?= $form->field($model, 'orderNumber', ['template' => 
                    '<div class="col-sm-9 col-sm-offset-2">{error}</div>
                    {label}
                    <div class="col-sm-9 input-group margin">
                        {input}
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-info btn-flat">
                                <span class="glyphicon glyphicon-search">
                            </button>
                        </span>{hint}
                    </div>',
                'inputOptions' => ['class' => 'form-control', 'placeholder' => '№ счета, например, T-8815'],
                'labelOptions' => ['class' => 'control-label col-sm-2'],
                'hintOptions' => ['class' => 'help-block']])->textInput(['required' => ($model->typeOfTrip && $model->typeOfTrip != 4) ? true : false]); ?>
            </div>
                <?= $form->field($model, 'subscribeOrder', $fieldOptions)->textarea()
                    ->hint('Введите описание продукции'); ?>
            <div id="listProducts" class="col-sm-10" hidden>
                <div class="col-sm-2"> <b>Список товаров</b> </div>
                <div class="col-sm-8">
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>Наименование</th>
                                <th>Кол-во</th>
                                <th>Ед.изм.</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row" id="size">
                <div class="col-sm-4">
                    <?= $form->field($model, 'length', ['template' => '<div class="col-sm-offset-6 col-sm-6">{error}</div>{label}<div class="col-sm-5">{input}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-6'],
                        'hintOptions' => ['class' => 'help-block']])->textInput()->error(false); ?>
                </div>
                <div class="col-sm-2">
                    <?= $form->field($model, 'width', ['template' => '<div class="col-sm-11">{error}</div>{label}<div class="col-sm-10">{input}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-6'],
                        'hintOptions' => ['class' => 'help-block']])->textInput()->label(false)->error(false); ?>
                </div>
                <div class="col-sm-2">
                    <?= $form->field($model, 'height', ['template' => '<div class="col-sm-11">{error}</div>{label}<div class="col-sm-10">{input}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-6'],
                        'hintOptions' => ['class' => 'help-block']])->textInput()->label(false)->error(false); ?>
                </div>
                <div class="col-sm-2" style="padding-top: 15px">
                    Объем: <span id="volume"><b></b></span> <b>м3</b>
                </div>
            </div>
            <?= $form->field($model, 'weightOrder', $fieldOptions)->textInput(); ?>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">Когда:</h3>
        </div>
        <div class="box-body">
            <?php is_numeric($model->firstDate) ? date('d.m.Y', $model->firstDate) : $model->firstDate; ?>
            <div <?= $edit ? 'hidden' : ''; ?> >
                <?= $form->field($model, 'firstDate', $fieldOptions)->widget(DatePicker::className(), [
                    'language' => 'ru',
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'autoclose'=>true,
                        'todayHighlight' => true
                    ]])->hint('Изначально планируемая дата поездки'); ?>
            </div>
            <?php if ($edit) : ?>
                <div style="margin-left: 50px; margin-bottom: 20px;"><b>Первичная дата </b><span style="margin-left: 30px;">
                <?= is_numeric($model->firstDate) ? date('d.m.Y', $model->firstDate) : $model->firstDate; ?></span></div>
                <div class="help-block col-sm-offset-2 col-sm-10" style="margin-top: -20px; margin-left: 170px;">Изначально планируемая дата поездки</div>
                <div class="row">
                    <div class="col-sm-4">
                        <?= $form->field($model, 'desiredDateFrom', ['template' => '<div class="col-sm-offset-6 col-sm-6">{error}</div>{label}<div class="col-sm-6">{input}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-6'],
                        'hintOptions' => ['class' => 'help-block']])->widget(DatePicker::className(), [
                            'language' => 'ru',
                            'type' => DatePicker::TYPE_INPUT,
                            'disabled' => $model->desiredDateFrom && !Yii::$app->user->identity->checkRule('driver', 3),
                            'pluginOptions' => [
                                'format' => 'dd.mm.yyyy',
                                'autoclose'=>true,
                                'todayHighlight' => true
                            ]]); ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'desiredDateTo', ['template' => '<div class="col-sm-11">{error}</div>{label}<div class="col-sm-8">{input}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-1'],
                        'hintOptions' => ['class' => 'help-block']])->widget(DatePicker::className(), [
                            'language' => 'ru',
                            'type' => DatePicker::TYPE_INPUT,
                            'disabled' => $model->desiredDateTo && !Yii::$app->user->identity->checkRule('driver', 3),
                            'pluginOptions' => [
                            'format' => 'dd.mm.yyyy',
                            'autoclose'=>true,
                            'todayHighlight' => true
                        ]])->label('-'); ?>
                    </div>
                    <div class="help-block col-sm-offset-2 col-sm-10" style="margin-top: -10px;">Даты поездки, согласованные менеджером</div>
                </div>
            <?php endif; ?>

            <?php if ($edit && $model->status != 1) : ?>
                <?= $form->field($model, 'dateTrip', $fieldOptions)->widget(DatePicker::className(), [
                        'language' => 'ru',
                        'type' => DatePicker::TYPE_INPUT,
                        'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'autoclose'=>true,
                        'todayHighlight' => true
                    ]])->hint('Дата поездки, определенная логистом'); ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header">
            <h3 class="box-title">Откуда:</h3>
        </div>
        <div class="box-body">
            <?= $form->field($model, 'from', $fieldOptions)->dropdownList(
                DriverAddress::find()->select(['name', 'id', 'from'])->where(['from' => 1])->indexBy('id')->column(),
                ['prompt' => '']
            ); ?>
            <?= $form->field($model, 'adressFrom', $fieldOptions)->textInput()
                ->hint('Введите по образцу: г. Москва, ул. Главная, д. 45, стр. 5, оф. 23'); ?>
            <input type="text" name="isAddressFrom" value="<?=$isAddressFrom?>" hidden>
            <?= $form->field($model, 'zoneFrom', $fieldOptions)->dropdownList(
                ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6'=> '6', '7' => '7', '8' => '8'],
                ['prompt' => '']
            ); ?>
            <div class="row">
                <div class="col-sm-6">
                     <?= $form->field($model, 'consignerName', ['template' => '<div class="col-sm-9 col-sm-offset-2">{error}</div>
                        {label}<div class="col-sm-8">{input}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-4'],
                        'hintOptions' => ['class' => 'help-block']])->textInput(); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'consignerPhone', ['template' => '<div class="col-sm-9 col-sm-offset-2">{error}</div>
                        {label}<div class="col-sm-7">{input}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-3'],
                        'hintOptions' => ['class' => 'help-block']])->textInput(); ?>
                </div>            
            </div>
            
            <?= $form->field($model, 'consignerInn', $fieldOptions)->textInput(); ?>
            
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'consignerUser', ['template' => '<div class="col-sm-9 col-sm-offset-2">{error}</div>
                        {label}<div class="col-sm-8">{input}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-4'],
                        'hintOptions' => ['class' => 'help-block']])->textInput(); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'consignerUserPhone', ['template' => '<div class="col-sm-9 col-sm-offset-2">{error}</div>
                        {label}<div class="col-sm-7">{input}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-3'],
                        'hintOptions' => ['class' => 'help-block']])->textInput(); ?>
                </div>
            </div>

            <?= $form->field($model, 'timeLoad', $fieldOptions)->textInput(); ?>
        </div>
    </div>

    <div class="box box-default box-solid">
        <div class="box-header">
            <h3 class="box-title">Куда:</h3>
        </div>
        <div class="box-body">
            <?= $form->field($model, 'to', $fieldOptions)->dropdownList(
                DriverAddress::find()->select(['name', 'id', 'from'])->where(['to' => 1])->indexBy('id')->column(),
                ['prompt' => '']
            ); ?>
            <?= $form->field($model, 'adressTo', $fieldOptions)->textInput()
                ->hint('Введите по образцу: г. Москва, ул. Главная, д. 45, стр. 5, оф. 23'); ?>
                <input type="text" name="isAddressTo" value="<?=$isAddressTo?>" hidden>
            <?= $form->field($model, 'zoneTo', $fieldOptions)->dropdownList(
                ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6'=> '6', '7' => '7', '8' => '8'],
                ['prompt' => '']
            ); ?>
            <div class="address_tk" <?=(!empty($model->terminalTC) ? "" : "hidden"); ?>>
                <?= $form->field($model, 'terminalTC', [
                    'template' => '
                        <div class="col-sm-9 col-sm-offset-2">{error}</div>
                        {label}
                        <div class="col-sm-9">{input}{hint}</div>
                    ',
                    'labelOptions' => ['class' => 'control-label col-sm-2'],
                    'hintOptions' => ['class' => 'help-block'],
                    'inputOptions' => ['required' => (!empty($model->terminalTC) ? true : false)]
                ])->textInput()->hint('Введите город терминала, либо адрес конечный'); ?>
            </div>

            <div class="row">
                <div class="col-sm-6">
                     <?= $form->field($model, 'consigneeName', ['template' => '<div class="col-sm-9 col-sm-offset-2">{error}</div>
                        {label}<div class="col-sm-8">{input}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-4'],
                        'hintOptions' => ['class' => 'help-block']])->textInput(); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'consigneePhone', ['template' => '<div class="col-sm-9 col-sm-offset-2">{error}</div>
                        {label}<div class="col-sm-7">{input}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-3'],
                        'hintOptions' => ['class' => 'help-block']])->textInput(); ?>
                </div>            
            </div>

                <?= $form->field($model, 'consigneeInn', $fieldOptions)->textInput(); ?>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'consigneeUser', ['template' => '<div class="col-sm-9 col-sm-offset-2">{error}</div>
                        {label}<div class="col-sm-8">{input}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-4'],
                        'hintOptions' => ['class' => 'help-block']])->textInput(); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'consigneeUserPhone', ['template' => '<div class="col-sm-9 col-sm-offset-2">{error}</div>
                        {label}<div class="col-sm-7">{input}{hint}</div>',
                        'labelOptions' => ['class' => 'control-label col-sm-3'],
                        'hintOptions' => ['class' => 'help-block']])->textInput(); ?>
                </div>
            </div>

            <?= $form->field($model, 'timeUnload', $fieldOptions)->textInput(); ?>
        </div>
    </div>

    <div class="box box-default">
        <div class="box-footer">
            <?php if (!$edit) : ?>
                <?= Html::submitButton('Создать', ['class' => 'btn btn-primary', 'formaction' => './create']); ?>
                <?= Html::a('Отмена', ['driver-trips/index'], ['class' => 'btn btn-danger']); ?>
            <?php else: ?>
                <?php if (in_array($model->status, [1, 2, 3, 4])) : ?>
                    <?= Html::submitButton('Сохранить', ['name' => 'save', 'value' => 'save', 'class' => 'btn btn-success', 'formaction' => './update?id='.$model->id]); ?>
                <?php endif; ?>
                
                <?= Html::submitButton('Отмена', ['name' => 'cancel', 'value' => 'cancel','class' => 'btn btn-danger', 'formaction' => './index']); ?>
                
                <?php if ($model->status == 1 && ($model->authorId == Yii::$app->user->identity->user_id || Yii::$app->user->identity->checkRule('driver', 6)) ) : ?>
                        <?= Html::submitButton('Подготовлена', ['name' => 'prepare', 'value' => 'prepare', 'class' => 'btn btn-primary', 'formaction' => './update?id='.$model->id]); ?>
                <?php endif; ?>

                <?php if (($model->status == 2 || $model->status == 4) && Yii::$app->user->identity->checkRule('driver', 2)) : ?>
                    <?= Html::submitButton('Поставить в план', ['name' => 'in_plan', 'value' => 'in_plan', 'class' => 'btn btn-warning', 'formaction' => './update?id='.$model->id]); ?>
                <?php endif; ?>
                <?php if ($model->status == 3 && ($model->authorId == Yii::$app->user->identity->user_id || Yii::$app->user->identity->checkRule('driver', 6)) ) : ?>
                        <?= Html::submitButton('Можно везти', ['name' => 'can_go', 'value' => 'can_go', 'class' => 'btn pink', 'formaction' => './update?id='.$model->id]); ?>
                <?php endif; ?>
                <?php if (in_array($model->status, [1, 2, 3, 4])  && ($model->authorId == Yii::$app->user->identity->user_id || Yii::$app->user->identity->checkRule('driver', 4)) ) : ?>
                    <?= Html::submitButton('Отменить поездку', ['name' => 'cancel_trip', 'value' => 'cancel_trip', 'class' => 'btn grey', 'formaction' => './update?id='.$model->id]); ?>
                <?php endif; ?>
                <?php if (in_array($model->status, [1, 2, 3, 4, 5]) ) : ?>
                    <?= Html::button('Комментарий', ['class' => 'btn grey', 'data-toggle' => 'modal', 'data-target' => '#modalComment']); ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php ActiveForm::end() ?>

    <div class="modal fade" id="modalComment" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Добаление комментария</h4>
                </div>
                <?php $formComment = ActiveForm::begin(); ?>
                    <div class="modal-body">
                        <?= $formComment->field($modelComment, 'comment')->textarea(); ?>
                        <input type="text" name="isAddressFrom" value="<?=$isAddressFrom?>" hidden>
                        <input type="text" name="isAddressTo" value="<?=$isAddressTo?>" hidden>
                    </div>
                    <div class="modal-footer">
                        <?= Html::button('Отмена', ['class' => 'btn btn-danger', 'data-dismiss' => 'modal']); ?>
                        <?= Html::submitButton('Сохранить',
                            ['name' => 'save_comment', 'value' => 'save_comment', 'class' => 'btn btn-primary', 'formaction' => './update?id='.$model->id]);
                        ?>
                    </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>

</div>