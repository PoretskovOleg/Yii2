<?php if ($stampFilename): ?>
    <img class="stamp" src="<?= Yii::getAlias('@old_host/' . $stampFilename) ?>">
<?php endif; ?>
<table cellspacing="1" cellpadding="1" class="signatures">
    <!-- split -->
    <tr class="top">
        <td colspan="5">

        </td>
    </tr>
    <!-- split -->
    <tr class="position">
        <td width="20%" class="post">Руководитель</td>
        <td width="27%">Генеральный директор</td>
        <td width="22%" class="sign-image-container">
            <?php if ($signFilename): ?>
                <img class="sign-image" src="<?= Yii::getAlias('@old_host/' . $signFilename) ?>">
            <?php endif; ?>
        </td>
        <td width="3%" style="border:none;"></td>
        <td><?= $model->signer->signatory_name ?></td>
    </tr>
    <!-- split -->
    <tr class="legend">
        <td></td>
        <td>должность</td>
        <td>подпись</td>
        <td></td>
        <td>расшифровка подписи</td>
    </tr>
    <!-- split -->
    <tr class="position">
        <td class="post" colspan="2">Главный (старший) бухгалтер</td>
        <td class="sign-image-container">
            <?php if ($signFilename): ?>
                <img class="sign-image" src="<?= Yii::getAlias('@old_host/' . $signFilename) ?>">
            <?php endif; ?>
        </td>
        <td style="border:none;"></td>
        <td><?= $model->signer->signatory_name ?></td>
    </tr>
    <!-- split -->
    <tr class="legend">
        <td colspan="2"></td>
        <td>подпись</td>
        <td style="border:none;"></td>
        <td>расшифровка подписи</td>
    </tr>
    <!-- split -->
    <tr class="position">
        <td class="post">Ответственный</td>
        <td>Менеджер</td>
        <td class="sign-image-container">
            <img class="sign-image" src="<?= Yii::getAlias('@old_host/upload/organization/sign/chenv.png') ?>">
        </td>
        <td style="border:none;"></td>
        <td>Чеботарев А. С.</td>
    </tr>
    <!-- split -->
    <tr class="legend">
        <td></td>
        <td>должность</td>
        <td>подпись</td>
        <td style="border:none;"></td>
        <td>расшифровка подписи</td>
    </tr>
</table>