<table cellspacing="1" cellpadding="1" class="goods">
    <tr>
        <!-- split -->
        <th width="6%">№п\п</th>
        <th width="6%">Код</th>
        <th width="50%">Товары (работы, услуги)</th>
        <th width="5%">Кол-во</th>
        <th width="5%">Ед. изм.</th>
        <th width="5%">Срок</th>
        <th width="12%">Цена с НДС (руб)</th>
        <th>Стоимость с НДС (руб)</th>
        <!-- split -->
    </tr>
    <?php foreach ($goods as $good): ?>
        <!-- split -->
        <tr>
            <td><?= $good['index'] ?></td>
            <td><?= $good['id'] ?></td>
            <td class="good-name"><?= $good['name'] ?></td>
            <td><?= $good['quantity'] ?></td>
            <td><?= $good['unit'] ?></td>
            <td><?= $good['delivery_period'] ?></td>
            <td style="text-align: right;"><?= number_format($good['price'], 2, ',', '') ?></td>
            <td style="text-align: right;"><?= number_format($good['amount'], 2, ',', '') ?></td>
        </tr>
        <!-- split -->
    <?php endforeach; ?>
</table>
