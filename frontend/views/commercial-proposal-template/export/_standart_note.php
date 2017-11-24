<!-- split -->
<table cellspacing="1" cellpadding="1">
    <!-- split -->
    <tr>
        <td colspan="2"><b>Порядок оплаты:</b></td>
    </tr>
    <!-- split -->
    <tr>
        <td width="5%"></td>
        <td><?= $model->prepayment_percentage ?>% - предоплата перед началом изготовления продукции</td>
    </tr>
    <!-- split -->
    <tr>
        <td colspan="2"><b>Срок изготовления:</b></td>
    </tr>
    <tr>
        <td></td>
        <td><?= $model->term_days ?> <?= \common\helpers\DateHelper::plural($model->term_days, 'день', 'дня', 'дней') ?></td>
    </tr>
    <tr>
        <td colspan="2"><b>Место отгрузки:</b></td>
    </tr>
    <tr>
        <td></td>
        <td><?= $model->delivery_stock->address ?></td>
    </tr>
</table>
<!-- split -->