<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>

<body>
    <!-- split -->
    <table cellspacing="0" cellpadding="1">
        <tr>
            <td width="70%">
                <?php if (!empty($brand->logo_filename)): ?>
                    <img class="logo" src="<?= Yii::getAlias('@brands/logos/' . $brand->logo_filename) ?>">
                <?php endif; ?>
            </td>
            <td>
                Тел:
                <span class="phones">
                    <?= $brand->phone ?><?= (!empty($brand->federal_phone)) ? ',<br>' . $brand->federal_phone : ''?>
                </span>
                <br>
                E-mail: <?= $brand->email ?>
            </td>
        </tr>
    </table>
    <!-- split -->
    <table cellspacing="0" cellpadding="0" class="pay-data">
        <tr>
            <td colspan="2">
                Банк получателя: <b><?= $model->organization->bank_name ?></b>
            </td>
            <td width="33%">
                БИК: <b><?= $model->organization->bik ?></b>
            </td>
        </tr>
        <tr>
            <td>
                ИНН: <b><?= $model->organization->inn ?></b>
            </td>
            <td width="33%">
                КПП: <b><?= $model->organization->kpp ?></b>
            </td>
            <td width="33%">
                K/C: <b><?= $model->organization->bank_cor_acc ?></b>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Получатель: <b><?= $model->organization->organization_name ?></b>
            </td>
            <td width="33%">
                Р/С: <b><?= $model->organization->bank_set_acc ?></b>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                Назначение платежа: <b>Оплата товара по счёту №<?= $model->id ?> от <?= $formatted_date ?></b>
            </td>
        </tr>
    </table>
    <!-- split -->
    <h2 style="margin-top: 20px;">Счёт на оплату №<?= $model->id ?> от <?= $formatted_date ?></h2>
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td style="vertical-align: top;" width="20%">
                Поставщик:
            </td>
            <td width="80%" style="padding-bottom: 5px;">
                <b>
                    <?= $model->organization->organization_name ?>,
                    ИНН: <?= $model->organization->inn ?>,
                    КПП: <?= $model->organization->kpp ?>,
                    Юр.адр: <?= $model->organization->legal_address ?>,
                    Факт.адр: <?= $model->organization->fact_address ?>,
                </b>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top;">
                Покупатель:
            </td>
            <td style="padding-bottom: 5px;">
                <b>
                    <?php if ($model->payer_organization): ?>
                        <?= $model->payer_organization->organization_name ?>,
                        ИНН: <?= $model->payer_organization->inn ?>,
                        КПП: <?= $model->payer_organization->kpp ?>,
                        Юр.адр: <?= $model->payer_organization->legal_address ?>,
                        Факт.адр: <?= $model->payer_organization->fact_address ?>,
                    <?php elseif ($model->payer_contact_person): ?>
                        <?= $payer_data ?>
                    <?php endif; ?>
                </b>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top;">
                Назначение платежа:
            </td>
            <td>
                <b>
                    Оплата товара по счёту №<?= $model->id ?> от <?= $formatted_date ?>
                </b>
            </td>
        </tr>
    </table>
    <!-- split -->