<?php

namespace common\models\Invoice;

use Yii;
use yii\web\UploadedFile;
use common\models\Old\Organization;
use common\models\Old\Contractor;
use common\models\Old\Subject;
use common\models\User;
use common\models\Old\ContactPerson;
use common\models\Brand;
use common\models\Old\Stock;
use common\models\Attachment;
use common\models\Old\Signer;


class Invoice extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_APPROVAL = 2;
    const STATUS_CORRECTION = 3;
    const STATUS_APPROVED = 4;
    const STATUS_SENT = 5;

    const DELIVERY_PAYMENT_TYPE_SEPARATE = 1;
    const DELIVERY_PAYMENT_TYPE_SUM = 2;

    const PAYMENT_METHOD_CASH = 1;
    const PAYMENT_METHOD_BANK = 2;

    public $catalogGoods;
    public $additionalGoods;
    private $_payer;
//    public $payer_organization;
//    public $payer_contact_person;
    public $attachments_files;
    private $_files;
    private $_comments;

    public static function tableName()
    {
        return 'invoices';
    }

    public function rules()
    {
        return [
            [
                [
                    'status_id',
                    'order_id',
                    'contractor_id',
                    'payer_organization_id',
                    'payer_contact_person_id',
                    'contact_person_id',
                    'subject_id',
                    'brand_id',
                    'manager_id',
                    'template_id',
                    'organization_id',
                    'signer_id',
                    'prepayment_percentage',
                    'term_days',
                    'delivery',
                    'delivery_payment_type',
                    'delivery_stock_id',
                    'payment_method',
                ],
                'integer'
            ],
            [
                [
                    'order_id',
                    'contractor',
                    'payer',
                    'contact_person',
                    'subject',
                    'brand',
                    'template',
                    'organization',
                    'signer',
                    'prepayment_percentage',
                    'term_days',
                    'payment_method',
                ],
                'required'
            ],
            [
                ['delivery_address', 'delivery_price'],
                'required',
                'when' => function($model) {
                    return $model->delivery == 1;
                },
                'whenClient' => 'function(attribute, value) { return $("#invoice-delivery").val() === "1"; }',
            ],
            [
                ['delivery_price'],
                'number',
                'numberPattern' => '/^[0-9]*[.,]?[0-9]+$/',
                'message' => 'Должно быть числом',
            ],
            [['delivery_address'], 'string'],
            [['created', 'note', 'catalogGoods', 'additionalGoods', 'payer', 'attachments_pages_ids', 'total'], 'safe'],
            [['attachments_files'], 'file', 'maxFiles' => 20],
            [['brand'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::className(), 'targetAttribute' => ['brand_id' => 'id']],
            [['manager'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['manager_id' => 'user_id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => InvoiceStatus::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['template'], 'exist', 'skipOnError' => true, 'targetClass' => InvoiceTemplate::className(), 'targetAttribute' => ['template_id' => 'id']],
            [['contractor'], 'exist', 'skipOnError' => true, 'targetClass' => Contractor::className(), 'targetAttribute' => ['contractor_id' => 'contractor_id']],
            [['contact_person'], 'exist', 'skipOnError' => true, 'targetClass' => ContactPerson::className(), 'targetAttribute' => ['contact_person_id' => 'contact_person_id']],
            [['subject'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'theme_id']],
            [['organization'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['organization_id' => 'organization_id']],
            [['signer'], 'exist', 'skipOnError' => true, 'targetClass' => Signer::className(), 'targetAttribute' => ['signer_id' => 'signatory_id']],
            [['delivery_stock'], 'exist', 'skipOnError' => true, 'targetClass' => Stock::className(), 'targetAttribute' => ['delivery_stock_id' => 'stock_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№',
            'status' => 'Статус',
            'order_id' => 'Заказ',
            'payer' => 'Плательщик',
            'contact_person' => 'Контактное лицо',
            'subject' => 'Тема',
            'brand' => 'Бренд',
            'manager' => 'Менеджер',
            'template' => 'Шаблон',
            'organization' => 'Юридическое лицо',
            'signer' => 'Подписант',
            'prepayment_percentage' => 'Процент предоплаты',
            'term_days' => 'Срок в днях',
            'payment_method' => 'Способ оплаты',
            'delivery_address' => 'Адрес доставки',
            'delivery_price' => 'Стоимость доставки',
            'delivery_stock' => 'Место отгрузки',
            'delivery_payment_type' => 'Расчёт доставки',
            'attachments_pages_ids' => 'Дополнительные листы',
            'created' => 'Дата и время создания',
        ];
    }

    public function addStatusComment($status_id) {
        $comment = new InvoiceComment();
        $comment->status_id = $status_id;
        $comment->invoice_id = $this->id;
        $comment->save();
    }

    public function handlePrimary() {
        $primary_ids = Yii::$app->request->post('primary');
        if (!empty($primary_ids)) {
            self::updateAll(['primary' => false], ['order_id' => $this->order_id]);
            self::updateAll(['primary' => true], ['id' => $primary_ids]);
        }
    }
    
    public function handleAttachments() {
        $attachments_files = UploadedFile::getInstances($this, 'attachments_files');
        if ($attachments_files) {
            $path = Yii::getAlias('@webroot') . Yii::getAlias('@invoices_attachments');
            if (\yii\helpers\FileHelper::createDirectory($path, $mode = 0775, $recursive = true)) {
                foreach ($attachments_files as $file) {
                    if ($file->saveAs($path . '/' . $file->baseName . '.' . $file->extension)) {
                        $cp_file = new InvoiceFile();
                        $cp_file->filename = $file->baseName . '.' . $file->extension;
                        $cp_file->invoice_id = $this->id;
                        $cp_file->save();
                    }
                }
            }
        }
    }
    
    public function hasChanges() {
        $dirtyAttributes = $this->getDirtyAttributes();
        $oldAttributes = $this->getOldAttributes();

        if (isset($dirtyAttributes['attachments_pages_ids']) && is_array($dirtyAttributes['attachments_pages_ids'])) {
            $dirtyAttributes['attachments_pages_ids'] = serialize($dirtyAttributes['attachments_pages_ids']);
        }

        foreach ($dirtyAttributes as $attribute => $value) {
            if ($oldAttributes[$attribute] != $value) {
                return true;
            }
        }

        $catalogGoods = \common\models\InvoiceGood::find()
            ->where(['invoice_id' => $this->id])
            ->andWhere(['not', ['good_id' => null]])
            ->orderBy('index')
            ->createCommand()
            ->queryAll()
        ;

        if (count(json_decode($this->catalogGoods, true)) != count($catalogGoods)) {
            return true;
        }

        foreach (json_decode($this->catalogGoods, true) as $key => $good) {
            foreach ($good as $attribute => $value) {
                if (isset($catalogGoods[$key][$attribute]) && $catalogGoods[$key][$attribute] != $value) {
                    return true;
                }
            }
        }

        $additionalGoods = \common\models\InvoiceGood::find()
            ->where(['invoice_id' => $this->id])
            ->andWhere(['good_id' => null])
            ->orderBy('index')
            ->createCommand()
            ->queryAll()
        ;

        if (count(json_decode($this->additionalGoods, true)) != count($additionalGoods)) {
            return true;
        }

        foreach (json_decode($this->additionalGoods, true) as $key => $good) {
            foreach ($good as $attribute => $value) {
                if (isset($additionalGoods[$key][$attribute]) && $additionalGoods[$key][$attribute] != $value) {
                    return true;
                }
            }
        }
        return false;
    }

    public function copyGoods($to_id) {
        $goods = InvoiceGood::findAll(['invoice_id' => $this->id]);
        foreach ($goods as $good) {
            $new_good = new InvoiceGood();
            $new_good->setAttributes($good->getAttributes());
            $new_good->invoice_id = $to_id;
            $new_good->save();
        }
    }

    public function handleGoods() {
        InvoiceGood::deleteAll(['invoice_id' => $this->id]);

        if (!empty($this->catalogGoods)) {
            $catalogGoods = json_decode($this->catalogGoods, true);

            foreach ($catalogGoods as $good) {
                $cp_good = new InvoiceGood();

                $cp_good->setAttributes($good);
                $cp_good->invoice_id = $this->id;
                $cp_good->save();
            }
        }

        if (!empty($this->additionalGoods)) {
            $additionalGoods = json_decode($this->additionalGoods, true);

            foreach ($additionalGoods as $good) {
                $cp_good = new InvoiceGood();

                $cp_good->setAttributes($good);
                $cp_good->invoice_id = $this->id;
                $cp_good->save();
            }
        }
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->manager_id = Yii::$app->user->id;
            $this->created = (new \DateTime())->format('Y-m-d H:i:s');
        }

        if (!empty($this->delivery_price)) {
            $this->delivery_price = str_replace(',', '.', $this->delivery_price);
        }

        if (is_array($this->attachments_pages_ids)) {
            $this->attachments_pages_ids = serialize($this->attachments_pages_ids);
        }

        if (!empty($this->catalogGoods)) {
            $catalogGoods = json_decode($this->catalogGoods, true);

            foreach ($catalogGoods as $good) {
                $this->total += $good['total'];
            }
        }

        if (!empty($this->additionalGoods)) {
            $additionalGoods = json_decode($this->additionalGoods, true);
            foreach ($additionalGoods as $good) {
                $this->total += $good['total'];
            }
        }

        $template = InvoiceTemplate::findOne($this->template_id);
        $this->note = $template->note;

        return parent::beforeSave($insert);
    }

    public function getExportData()
    {
        $data['brand'] = $this->brand;

        $data['stampFilename'] = $this->organization->getStampFilename();
        $data['signFilename'] = $this->signer->getSignFilename();

        $units = [];
        foreach (\common\models\Old\Unit::find()->createCommand()->queryAll() as $unit) {
            $units[$unit['list_unit_id']] = $unit['list_unit_name'];
        }

        $data['goods'] = [];
        $good_index = 1;

        $goods = InvoiceGood::find()
            ->where(['invoice_id' => $this->id])
            ->andWhere(['not', ['good_id' => null]])
            ->orderBy('index')
            ->createCommand()
            ->queryAll()
        ;

        foreach ($goods as $good) {
            $good_price = $good['end_price'];

            if ($this->delivery && $this->delivery_payment_type == self::DELIVERY_PAYMENT_TYPE_SUM) {
                $good_price = $good_price + round(($this->delivery_price / $this->total) * $good_price, 2);
            }

            $data['goods'][] = [
                'id' => $good['good_id'],
                'name' => $good['name'],
                'index' => $good_index,
                'unit' => $units[$good['unit_id']],
                'quantity' => $good['quantity'],
                'delivery_period' => $good['delivery_period'],
                'price' => $good_price,
                'amount' => $good_price * $good['quantity'],
            ];
            $good_index++;
        }

        $goods = InvoiceGood::find()
            ->where(['invoice_id' => $this->id])
            ->andWhere(['good_id' => null])
            ->orderBy('index')
            ->createCommand()
            ->queryAll()
        ;

        foreach ($goods as $good) {
            $good_price = $good['end_price'];

            if ($this->delivery && $this->delivery_payment_type == self::DELIVERY_PAYMENT_TYPE_SUM) {
                $good_price = $good_price + round(($this->delivery_price / $this->total) * $good_price, 2);
            }

            $data['goods'][] = [
                'id' => '',
                'name' => $good['name'],
                'index' => $good_index,
                'unit' => $units[$good['unit_id']],
                'quantity' => $good['quantity'],
                'delivery_period' => $good['delivery_period'],
                'price' => $good_price,
                'amount' => $good_price * $good['quantity'],
            ];
            $good_index++;
        }

        if ($this->delivery && $this->delivery_payment_type == self::DELIVERY_PAYMENT_TYPE_SEPARATE) {
            $data['goods'][] = [
                'name' => 'Доставка',
                'id' => '',
                'index' => count($data['goods']) + 1,
                'unit' => '-',
                'quantity' => '-',
                'price' => $this->delivery_price,
                'amount' => $this->delivery_price,
            ];
        }

        $data['goodsTotal'] = 0;
        foreach ($data['goods'] as $good) {
            $data['goodsTotal'] += $good['amount'];
        }

        if (strripos($this->signer->signatory_reason, 'доверенности') !== false) {
            $data['signatory_reason'] = '(по ' . $this->signer->signatory_reason . ')';
        } else {
            $data['signatory_reason'] = false;
        }

        $months = [
            'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря',
        ];
        $date = new \DateTime($this->created);
        $data['formatted_date'] =
            $date->format('j') . ' '
            . $months[(int)$date->format('n') - 1] . ' '
            . $date->format('Y') . ' г.';


        if ($this->payer_contact_person) {
            $payer_data = $this->payer_contact_person->contact_person_name;
            if ($this->payer_contact_person->phone_number || $this->payer_contact_person->mobile_phone_number) {
                $payer_data .= ', Тел: ';
                $payer_data .= $this->payer_contact_person->phone_number ? $this->payer_contact_person->phone_number : '';
                $payer_data .= $this->payer_contact_person->mobile_phone_number ? ', ' . $this->payer_contact_person->mobile_phone_number : '';
            }

            if ($this->payer_contact_person->email) {
                $payer_data .= ', Email: ' . $this->payer_contact_person->email;
            }

            if ($this->contractor->address) {
                $payer_data .= ', Адрес: ' . $this->contractor->address;
            }

            if ($this->payer_contact_person->passport_seria && $this->payer_contact_person->passport_number) {
                $payer_data .= ', Серия и номер паспорта: ' . $this->payer_contact_person->passport_seria;
                $payer_data .= $this->payer_contact_person->passport_number;
            }

            if ($this->payer_contact_person->passport_issue_by && $this->payer_contact_person->passport_issue_date) {
                $payer_data .= ', Выдан: ';
                $payer_data .= $this->payer_contact_person->passport_issue_by;
                $payer_data .= ' ' . (new \DateTime($this->payer_contact_person->passport_issue_date))->format('d.m.Y г.');
            }
            $data['payer_data'] = $payer_data . ',';
        }

        $total = explode(',', number_format($data['goodsTotal'], 2, ',', ''));
        $total_string = Yii::$app->i18n->format('{n, spellout} {n, plural, one{рубль} few{рубля} many{рублей} other{рублей}}',
            ['n' => $total[0]], 'ru-RU');
        if (count($total) > 1 && $total[1] > 0) {
            $total_string .= Yii::$app->i18n->format(' {n, spellout} {n, plural, one{копейка} few{копейки} many{копеек} other{копеек}}',
                ['n' => $total[1]], 'ru-RU');
        }

        $tax = explode(',',
            number_format($data['goodsTotal'] - $data['goodsTotal'] / 1.18, 2, ',', ''));
        $tax_string = Yii::$app->i18n->format('{n, spellout} {n, plural, one{рубль} few{рубля} many{рублей} other{рублей}}',
            ['n' => $tax[0]], 'ru-RU');
        if (count($tax) > 1 && $tax[1] > 0) {
            $tax_string .= Yii::$app->i18n->format(' {n, spellout,%spellout-cardinal-feminine} {n, plural, one{копейка} few{копейки} many{копеек} other{копеек}}',
                ['n' => $tax[1]], 'ru-RU');
        }
        $data['tax_string'] = $tax_string;
        $data['total_string'] = $total_string;

        return $data;
    }


    public function getFormData() {
        if (!is_array($this->attachments_pages_ids)) {
            $this->attachments_pages_ids = unserialize($this->attachments_pages_ids);
        }

        if (!empty($this->delivery_price)) {
            $this->delivery_price = str_replace('.', ',', floatval($this->delivery_price));
        }

        $payers = [];
        $contact_persons = [];
        if (!empty($this->contractor_id)) {
            foreach (Organization::find()->where(['contractor_id' => $this->contractor_id])->all() as $row) {
                $payers['o_' . $row['organization_id']] = $row['organization_name'];
            }

            foreach (ContactPerson::find()->where(['contractor_id' => $this->contractor_id])->all() as $row) {
                $payers['cp_' . $row['contact_person_id']] = $row['contact_person_name'];
                $contact_persons[$row['contact_person_id']] = $row['contact_person_name'];
            }
        }

        $brands = [];
        foreach (Brand::find()->all() as $row) {
            $brands[$row['id']] = $row['title'];
        }

        $subjects = [];
        foreach (Subject::find()->all() as $row) {
            $subjects[$row['theme_id']] = $row['name'];
        }

        $pay_methods = [
            '1' => 'Банковский перевод',
            '2' => 'Наличными',
        ];

        $templates = [];
        foreach (InvoiceTemplate::find()->all() as $row) {
            $templates[$row['id']] = $row['name'];
        }

        $organizations = [];
        foreach (Organization::findAll(['contractor_id' => 13463]) as $row) {
            $organizations[$row['organization_id']] = $row['organization_name'];
        }

        $signers = [];
        if ($this->organization) {
            foreach (Signer::findAll(['organization_id' => $this->organization]) as $row) {
                $signers[$row['signatory_id']] = $row['signatory_name'];
            }
        }

        $stocks = [];
        foreach (Stock::find()->all() as $row) {
            $stocks[$row['stock_id']] = $row['stock_name'];
        }

        $attachments = [];
        foreach (Attachment::find()->all() as $row) {
            $attachments[$row['id']] = $row['name'];
        }

        $emailModel = new InvoiceEmail();
        $commentModel = new InvoiceComment();
        $commentModel->invoice_id = $this->id;

        $linked_proposals = [];
        if (!$this->isNewRecord) {
            $linked_proposals = Invoice::find()
                ->where(['order_id' => $this->order_id])
                ->andWhere(['<>', 'id', $this->id])
                ->all();

            $emailModel->email = '';//$this->contact_person->email;
            $emailModel->subject = 'Счёт №' . $this->id
                . ' от ' . (new \DateTime($this->created))->format('d.m.Y')
                . ' от ' . $this->brand->title;

            $emailModel->text = "Здравствуйте, " . $this->contact_person->contact_person_name . ".\nВ приложении счёт. Если будут вопросы, звоните.\n\nС уважением,\nМенеджер " . $this->manager->last_name . " " . mb_substr($this->manager->first_name, 0, 1) . ". " . mb_substr($this->manager->patronymic, 0, 1) . ".\n" . $this->brand->title;

            if (!empty($this->manager->phone_number)) {
                $emailModel->text .= "\nТел. моб.: " . $this->manager->phone_number;
            }

            if (!empty($this->manager->work_email)) {
                $emailModel->text .= "\nЕ-Mail: " . $this->manager->work_email;
            }

            $emailModel->invoice_filename =
                'Счёт №' . $this->id
                . ' от ' . (new \DateTime($this->created))->format('d.m.Y')
                . ' ' . $this->brand->title . '.pdf'
            ;
        }

        return [
            'subjects' => $subjects,
            'organizations' => $organizations,
            'signers' => $signers,
            'stocks' => $stocks,
            'attachments' => $attachments,
            'linked_proposals' => $linked_proposals,
            'pay_methods' => $pay_methods,
            'templates' => $templates,
            'brands' => $brands,
            'contact_persons' => $contact_persons,
            'payers' => $payers,
            'emailModel' => $emailModel,
            'commentModel' => $commentModel,
        ];
    }

    public function getComments() {
        return $this->hasMany(InvoiceComment::className(), ['invoice_id' => 'id'])
            ->orderBy(['created' => SORT_DESC]);
    }

    public function getFiles() {
        return $this->hasMany(InvoiceFile::className(), ['invoice_id' => 'id']);
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::className(), ['id' => 'brand_id']);
    }
    public function setBrand($value)
    {
        $this->brand_id = $value;
    }

    public function getContractor()
    {
        return $this->hasOne(Contractor::className(), ['contractor_id' => 'contractor_id']);
    }
    public function setContractor($value)
    {
        $this->contractor_id = $value;
    }

    public function getContact_person()
    {
        return $this->hasOne(ContactPerson::className(), ['contact_person_id' => 'contact_person_id']);
    }
    public function setContact_person($value)
    {
        $this->contact_person_id = $value;
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['theme_id' => 'subject_id']);
    }
    public function setSubject($value)
    {
        $this->subject_id = $value;
    }

    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['organization_id' => 'organization_id']);
    }
    public function setOrganization($value)
    {
        $this->organization_id = $value;
    }

    public function getSigner()
    {
        return $this->hasOne(Signer::className(), ['signatory_id' => 'signer_id']);
    }
    public function setSigner($value)
    {
        $this->signer_id = $value;
    }

    public function getPayer_organization() {
        return $this->hasOne(Organization::className(), ['organization_id' => 'payer_organization_id']);
    }

    public function getPayer_contact_person() {
        return $this->hasOne(ContactPerson::className(), ['contact_person_id' => 'payer_contact_person_id']);
    }

    public function getPayer()
    {
        if (!is_null($this->payer_organization_id)) {
            return 'o_' . $this->payer_organization_id;
        } else if (!is_null($this->payer_contact_person_id)) {
            return 'cp_' . $this->payer_contact_person_id;

        }

        return false;
    }
    public function setPayer($value)
    {
        $value = explode('_', $value);
        if (count($value) > 1) {
            if ($value[0] === 'o') {
                $this->payer_organization_id = $value[1];
                $this->payer_contact_person_id = null;
            } else if ($value[0] === 'cp') {
                $this->payer_contact_person_id = $value[1];
                $this->payer_organization_id = null;
            }
        }
    }

    public function getDelivery_stock()
    {
        return $this->hasOne(Stock::className(), ['stock_id' => 'delivery_stock_id']);
    }
    public function setDelivery_stock($value)
    {
        $this->delivery_stock_id = $value;
    }

    public function getManager()
    {
        return $this->hasOne(User::className(), ['user_id' => 'manager_id']);
    }
    public function setManager($value)
    {
        $this->manager_id = $value;
    }

    public function getStatus()
    {
        return $this->hasOne(InvoiceStatus::className(), ['id' => 'status_id']);
    }
    public function setStatus($value)
    {
        $this->status_id = $value;
    }

    public function getTemplate()
    {
        return $this->hasOne(InvoiceTemplate::className(), ['id' => 'template_id']);
    }
    public function setTemplate($value)
    {
        $this->template_id = $value;
    }
}
