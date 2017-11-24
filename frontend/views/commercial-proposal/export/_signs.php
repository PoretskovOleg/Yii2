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
            <td width="20%" class="post">Генеральный директор</td>
            <td width="15%" class="sign-image-container">
                <?php if ($signFilename): ?>
                    <img class="sign-image" src="<?= Yii::getAlias('@old_host/' . $signFilename) ?>">
                <?php endif; ?>
            </td>
            <td class="space" width="2%"></td>
            <td width="15%"><?= $model->signer->signatory_name ?></td>
            <td class="space" width="20%"><?= $signatory_reason ?></td>
        <?php else: ?>
            <td width="30%" class="post">Генеральный директор</td>
            <td width="25%" class="sign-image-container">
                <?php if ($signFilename): ?>
                    <img class="sign-image" src="<?= Yii::getAlias('@old_host/' . $signFilename) ?>">
                <?php endif; ?>
            </td>
            <td class="space" width="2%"></td>
            <td colspan="3"><?= $model->signer->signatory_name ?></td>
        <?php endif; ?>
    </tr>
    <!-- split -->
    <tr class="legend">
        <td></td>
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
        <td class="post">Главный (старший) бухгалтер</td>
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
        <td></td>
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
        <td class="post">Менеджер</td>
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
        <td></td>
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