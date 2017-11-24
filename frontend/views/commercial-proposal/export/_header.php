<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>

<body>
    <!-- split -->
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td width="60%">
                <?php if (!empty($brand->logo_filename)): ?>
                    <img class="logo" src="<?= Yii::getAlias('@brands/logos/' . $brand->logo_filename) ?>">
                <?php endif; ?>
            </td>
            <td width="40%">
                Тел:
                <span class="phones">
                    <?= $brand->phone ?><?= (!empty($brand->federal_phone)) ? ',<br>' . $brand->federal_phone : ''?>
                </span>
                <br>
                Сайт: <span class="website"><?= $brand->website ?></span>
                <br>
                E-mail: <?= $brand->email ?>
            </td>
        </tr>
        <tr>
            <td><span class="indent">Заказчик:</span> <?= $model->contact_person->contact_person_name ?></td>
            <td><span class="indent">Исполнитель:</span> <?= $model->manager->last_name . ' ' . $model->manager->first_name ?></td>
        </tr>
        <tr>
            <?php
                $contacts = [];
                if (!empty($model->contact_person->mobile_phone_number)) {
                    $contacts[] = $model->contact_person->mobile_phone_number;
                }
                if (!empty($model->contact_person->phone_number)) {
                    $contacts[] = $model->contact_person->phone_number;
                }
                if (!empty($model->contact_person->email)) {
                    $contacts[] = $model->contact_person->email;
                }
            ?>

            <td><span class="indent">Контакты:</span> <?= implode(', ', $contacts) ?></td>
            <td><span class="indent">Моб:</span> <?= $model->manager->phone_number ?></td>
        </tr>
    </table>
    <div class="breakline"></div>
    <!-- split -->
    <h2>Уважаемый <?= $model->contact_person->contact_person_name ?>!</h2>
    <h3>
        По Вашему запросу компания <?= $brand->title ?> произвела расчет стоимости товаров и услуг
    </h3>