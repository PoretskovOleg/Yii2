<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\widgets\LinkPager;

function renderContractor($data) {
    $html = '';

    $html .= '<table class="contractors-info">';
    if (!empty($data->organizations)) {
        $html .= '<tr class="organization"><td width="5%"></td>';
        $html .= '<td width="30%">Юр. лицо</td>';
        $html .= '<td width="35%">Юр. адрес</td>';
        $html .= '<td>Тел., email</td>';
        $html .= '</tr>';
        foreach ($data->organizations as $organization) {
            $html .= '<tr>';
            $html .= '
                <td class="radio-input"><input 
                    type="radio" 
                    name="organizations_' . $data->contractor_id . '"
                    class="organizations-radio-' . $data->contractor_id . '"
                    value="o_' . $organization->organization_id . '"
                    >
                </td>
            ';
            $html .= '<td>' . $organization->organization_name . ' (ИНН ' . $organization->inn . ')</td>';
            $html .= '<td>' . $organization->legal_address . '</td>';

            $contacts = [];
            if (!empty($organization->phone_number)) {
                $contacts[] = $organization->phone_number;
            }
            if (!empty($organization->email)) {
                $contacts[] = $organization->email;
            }

            $html .= '<td>' . implode(', ', $contacts) . '</td>';
            $html .= '</tr>';
        }
    }

    if (!empty($data->contact_persons)) {
        $html .= '<tr class="individual"><td width="5%"></td>';
        $html .= '<td width="30%">Физ. лицо</td>';
        $html .= '<td width="35%">Должность</td>';
        $html .= '<td>Тел., email</td>';
        $html .= '</tr>';
        foreach ($data->contact_persons as $individual) {
            $html .= '<tr>';
            $html .= '
                <td class="radio-input"><input 
                    type="radio" 
                    name="organizations_' . $data->contractor_id . '"
                    class="organizations-radio-' . $data->contractor_id . '"
                    value="cp_' . $individual->contact_person_id . '"
                    >
                </td>
                ';
            $html .= '<td>' . $individual->contact_person_name . '</td>';
            $html .= '<td>' . $individual->post . '</td>';

            $contacts = [];
            if (!empty($individual->mobile_phone_number)) {
                $contacts[] = $individual->mobile_phone_number;
            }
            if (!empty($individual->phone_number)) {
                $contacts[] = $individual->phone_number;
            }
            if (!empty($individual->email)) {
                $contacts[] = $individual->email;
            }

            $html .= '<td>' . implode(', ', $contacts) . '</td>';
            $html .= '</tr>';
        }
    }

    if (!empty($data->contact_persons)) {
        $html .= '<tr class="contact-person"><td width="5%"></td>';
        $html .= '<td width="30%">Контактное лицо</td>';
        $html .= '<td width="35%">Должность</td>';
        $html .= '<td>Тел., email</td>';
        $html .= '</tr>';
        foreach ($data->contact_persons as $contact_person) {
            $html .= '<tr>';
            $html .= '
                <td class="radio-input"><input 
                    type="radio" 
                    name="contact_persons_' . $data->contractor_id . '"
                    class="contact_persons-radio-' . $data->contractor_id . '"
                    value="' . $contact_person->contact_person_id . '"
                    ></td>
            ';
            $html .= '<td>' . $contact_person->contact_person_name . '</td>';
            $html .= '<td>' . $contact_person->post . '</td>';

            $contacts = [];
            if (!empty($contact_person->mobile_phone_number)) {
                $contacts[] = $contact_person->mobile_phone_number;
            }
            if (!empty($contact_person->phone_number)) {
                $contacts[] = $contact_person->phone_number;
            }
            if (!empty($contact_person->email)) {
                $contacts[] = $contact_person->email;
            }

            $html .= '<td>' . implode(', ', $contacts) . '</td>';
            $html .= '</tr>';
        }
    }

    $html .= '</table>';
    return $html;
}

$fieldOptions = [
    'template' => '
            <div class="col-md-12">
                {label}
                {input}{hint}
            </div>
    ',
    'hintOptions' => [
        'class' => 'help-block',
    ],
    'labelOptions' => [
        'style' => 'margin-right: 10px;'
    ],
];

?>

<?php Pjax::begin(['id' => 'contractors', 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="box box-solid invoice-search">
    <div class="box-header">
        <?php $form = ActiveForm::begin([
            'action' => 'search-contractors',
            'successCssClass' => false,
            'id' => 'contractor_search_form',
            'options' => [
                'data-pjax' => true,
                'class' => 'form-inline',
            ],
        ]); ?>

            <?= $form->field($contractorSearchModel, 'name', $fieldOptions)
                ->textInput(['class' => 'form-control contractor-search-input'])
            ?>
            <?= $form->field($contractorSearchModel, 'inn', $fieldOptions)
                ->textInput(['class' => 'form-control contractor-search-input'])
            ?>
            <?= $form->field($contractorSearchModel, 'email', $fieldOptions)
                ->textInput(['class' => 'form-control contractor-search-input'])
            ?>
            <?= $form->field($contractorSearchModel, 'phone', $fieldOptions)
                ->widget(\yii\widgets\MaskedInput::className(), [
                    'mask' => '(999)999-9999',
                    'class' => 'form-control contractor-search-input',
                ])
            ?>
            <div class="form-group">
                <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
            </div>

        <?= $form->field($contractorSearchModel, 'page')->hiddenInput(['id' => 'selected_page', 'class' => ''])->label(false) ?>
        <?php ActiveForm::end(); ?>
    </div>

    <?php if (!empty($contractors)): ?>
    <div class="box-body table-responsive">
        <table class="table table-striped table-bordered table-contractors">
            <thead>
                <tr>
                    <th width="5%">Выбрать</th>
                    <th width="15%">Контрагент</th>
                    <th></th>
                </tr>
            </thead>

            <?php foreach ($contractors as $contractor): ?>
                <tr class="contractor">
                    <td class="select-button">
                       <?= Html::button('Выбрать', [
                           'class' => 'contractor-select-btn btn btn-default',
                           'data-contractor-id' => $contractor->contractor_id,
                       ]) ?>
                    </td>

                    <td class="contractor-info">
                        <b><?= $contractor->contractor_name ?></b><br>
                        <?php if (!empty($contractor->type0)): ?>
                            Тип: <?= $contractor->type0->type_name ?><br>
                        <?php endif; ?>
                        <?php if (!empty($contractor->manager)): ?>
                            Менеджер: <?= $contractor->manager->first_name ?> <?= $contractor->manager->last_name ?>
                        <?php endif; ?>
                    </td>

                    <td class="container-contractors-info">
                        <?= renderContractor($contractor) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="box-footer">
        <?= LinkPager::widget([
            'pagination' => $pages,
            'linkOptions' => [
                'class' => 'pagination-link'
            ],
        ]) ?>
    </div>
    <?php else: ?>
        <div class="box-body">
            <div class="col-md-12 text-center">
                Ничего не найдено
            </div>
        </div>
    <?php endif; ?>
</div>
<?php Pjax::end(); ?>