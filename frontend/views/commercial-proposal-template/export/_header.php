<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>

<body>
    <!-- split -->
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td width="60%"><img class="logo" src="<?= Yii::getAlias('@web/images/commercial-proposal-template/test_brand_logo.png') ?>"></td>
            <td width="40%">
                Тел:
                <span class="phones">
                    <?= $brand->phone ?><?= (!empty($brand->federal_phone)) ? ',<br>' . $brand->federal_phone : ''?>
                </span>
                <br>
                Сайт:
                <a href="<?= $brand->website ?>">
                    <?= $brand->website ?>
                </a>
                <br>
                E-mail:
                <a href="mailto:<?= $brand->email ?>">
                    <?= $brand->email ?>
                </a>
            </td>
        </tr>
        <tr>
            <td><span class="indent">Заказчик:</span> Сергей</td>
            <td><span class="indent">Исполнитель:</span> Чеботарев Антон</td>
        </tr>
        <tr>
            <td><span class="indent">Контакты:</span> +7 (999) 999-99-99, test@test.ru</td>
            <td><span class="indent">Моб:</span> +7 (999) 999-99-99</td>
        </tr>
    </table>
    <div class="breakline"></div>
    <!-- split -->
    <h2>Уважаемый Сергей!</h2>
    <h3>
        По Вашему запросу <?= $brand->title ?> произвела расчет стоимости услуг
    </h3>