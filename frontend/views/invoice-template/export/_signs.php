<?php if ($stampFilename): ?>
    <img class="<?= $signatory_reason ? 'stamp' : 'stamp-wide' ?>" src="<?= Yii::getAlias('@old_host/' . $stampFilename) ?>">
<?php endif; ?>
<table cellspacing="1" cellpadding="1" class="signatures">
    <!-- split -->
    <tr class="top">
        <td colspan="5">

        </td>
    </tr>
    <!-- split -->
    <tr class="position">
        <?php if ($signatory_reason): ?>
            <td width="20%">Генеральный директор</td>
            <td class="space" width="1%"></td>
            <td width="15%" class="sign-image-container">
                <?php if ($signFilename): ?>
                    <img class="sign-image" src="<?= Yii::getAlias('@old_host/' . $signFilename) ?>">
                <?php endif; ?>
            </td>
            <td class="space" width="1%"></td>
            <td width="15%"><?= $model->signer->signatory_name ?></td>
            <td class="space" width="20%"><?= $signatory_reason ?></td>
        <?php else: ?>
            <td width="30%">Генеральный директор</td>
            <td class="space" width="1%"></td>
            <td width="25%" class="sign-image-container">
                <?php if ($signFilename): ?>
                    <img class="sign-image" src="<?= Yii::getAlias('@old_host/' . $signFilename) ?>">
                <?php endif; ?>
            </td>
            <td class="space" width="1%"></td>
            <td colspan="3"><?= $model->signer->signatory_name ?></td>
        <?php endif; ?>
    </tr>
    <!-- split -->
    <tr class="legend">
        <td>должность</td>
        <td class="space"></td>
        <td>подпись</td>
        <td class="space"></td>
        <?php if ($signatory_reason): ?>
            <td>расшифровка подписи</td>
            <td></td>
        <?php else: ?>
            <td colspan="3">расшифровка подписи</td>
        <?php endif; ?>
    </tr>
    <!-- split -->
    <tr class="position">
        <td>Главный (старший) бухгалтер</td>
        <td class="space"></td>
        <td class="sign-image-container">
            <?php if ($signFilename): ?>
                <img class="sign-image" src="<?= Yii::getAlias('@old_host/' . $signFilename) ?>">
            <?php endif; ?>
        </td>
        <td class="space"></td>
        <?php if ($signatory_reason): ?>
            <td width="15%"><?= $model->signer->signatory_name ?></td>
            <td class="space" width="20%"><?= $signatory_reason ?></td>
        <?php else: ?>
            <td colspan="3"><?= $model->signer->signatory_name ?></td>
        <?php endif; ?>
    </tr>
    <!-- split -->
    <tr class="legend">
        <td>должность</td>
        <td class="space"></td>
        <td>подпись</td>
        <td class="space"></td>
        <?php if ($signatory_reason): ?>
            <td>расшифровка подписи</td>
            <td></td>
        <?php else: ?>
            <td colspan="3">расшифровка подписи</td>
        <?php endif; ?>
    </tr>
    <!-- split -->
    <tr class="position">
        <td>Менеджер</td>
        <td class="space"></td>
        <td class="sign-image-container">
            <img class="sign-image" src="<?= Yii::getAlias('@old_host/' . $signFilename) ?>">
        </td>
        <td class="space"></td>

        <?php if ($signatory_reason): ?>
            <td><?= $model->signer->signatory_name ?></td>
        <?php else: ?>
            <td colspan="3"><?= $model->signer->signatory_name ?></td>
        <?php endif; ?>
    </tr>
    <!-- split -->
    <tr class="legend">
        <td>должность</td>
        <td class="space"></td>
        <td>подпись</td>
        <td class="space"></td>
        <?php if ($signatory_reason): ?>
            <td>расшифровка подписи</td>
            <td></td>
        <?php else: ?>
            <td colspan="3">расшифровка подписи</td>
        <?php endif; ?>
    </tr>
</table>