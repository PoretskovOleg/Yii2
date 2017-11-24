<!-- split -->
<table cellspacing="1" cellpadding="1">
    <!-- split -->
    <tr>
        <td><b>Всего к оплате:</b> <?= $total_string ?>, в том числе НДС(18%) - <?= $tax_string ?></td>
    </tr>
    <!-- split -->
    <tr class="delivery">
        <?php if ($model->delivery): ?>
            <td>Адрес доставки: <b><?= $model->delivery_address ?></b></td>
        <?php else: ?>
            <td>Место отгрузки: <b><?= $model->delivery_stock->address ?></b></td>
        <?php endif; ?>
    </tr>
    <!-- split -->
</table>
<!-- split -->