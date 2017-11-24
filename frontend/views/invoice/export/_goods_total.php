<!-- split -->
<table cellspacing="1" cellpadding="1" class="goods-total">
    <tr>
        <td width="89%">Итого:</td>
        <td><?= number_format($goodsTotal, 2, ',', '') ?></td>
    </tr>
    <tr>
        <td>В том числе НДС 18%:</td>
        <td><?= number_format($goodsTotal - $goodsTotal / 1.18, 2, ',', '') ?></td>
    </tr>
    <tr>
        <td><b>Всего к оплате:</b></td>
        <td><?= number_format($goodsTotal, 2, ',', '') ?></td>
    </tr>
</table>
<!-- split -->