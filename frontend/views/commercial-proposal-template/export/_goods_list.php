<table cellspacing="1" cellpadding="1" class="goods">
    <tr>
        <th width="5%">п\п</th>
        <th width="53%">Наименование</th>
        <th width="7%">Ед. изм.</th>
        <th width="10%">Кол-во</th>
        <th width="12%">Цена,р.ед с НДС</th>
        <th>Цена,р. ИТОГО с НДС</th>
    </tr>
    <?php foreach ($goods as $good): ?>
        <!-- split -->
        <tr>
            <td><?= $good['order'] ?></td>
            <td class="good-name"><?= $good['name'] ?></td>
            <td><?= $good['unit'] ?></td>
            <td><?= $good['quantity'] ?></td>
            <td><?= number_format($good['price'], 2, ',', '') ?></td>
            <td><?= number_format($good['amount'], 2, ',', '') ?></td>
        </tr>
        <!-- split -->
    <?php endforeach; ?>
</table>
