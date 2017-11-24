<?php

namespace common\models\CommercialProposal;

use Yii;
use yii\web\UploadedFile;
use common\models\Old\Organization;
use common\models\Old\Subject;
use common\models\Brand;
use common\models\Old\Stock;
use common\models\Old\Position;
use common\models\Attachment;
use common\models\Old\Signer;


class CommercialProposalTemplate extends \yii\db\ActiveRecord
{
    public $promo_2_file, $promo_5_file, $promo_8_file;

    public static function tableName()
    {
        return 'commercial_proposal_templates';
    }

    public function rules()
    {
        return [
            [
                [
                    'name',
                    'subject',
                    'amount_greater_than',
                    'amount_less_than',
                    'organization',
                    'signer',
                    'prepayment_percentage',
                    'term_days',
                    'delivery_stock',
                ],
                'required',
                'message' => 'Поле не может быть пустым',
            ],

            [
                ['term_days', 'prepayment_percentage'],
                'integer',
                'message' => 'Должно быть целым числом',
            ],
            [
                ['prepayment_percentage'],
                'integer',
                'min' => 0,
                'max' => 100,
                'tooBig' => 'Не может быть больше 100',
                'tooSmall' => 'Не может быть меньше 0',
            ],
            [
                [
                    'amount_greater_than',
                    'amount_less_than',
                ],
                'number',
                'numberPattern' => '/^[0-9]*[.,]?[0-9]+$/',
                'message' => 'Должно быть числом',
            ],
            [['needs_approval_by_ids', 'attachments_pages_ids'], 'safe'],
            [['note'], 'string'],
            [
                [
                    'promo_2_file',
                    'promo_5_file',
                    'promo_8_file'
                ],
                'file',
                'maxSize' => 2 * 1024 * 1024,
                'tooBig' => 'Файл слишком большой',
                'extensions' => 'png, jpeg, jpg',
                'wrongExtension' => 'Файл имеет недопустимый тип',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№',
            'name' => 'Название',
            'subject' => 'Тема',
            'amount_greater_than' => 'Сумма больше, руб.',
            'amount_less_than' => 'Сумма меньше, руб.',
            'needs_approval_by_ids' => 'Утверждается кем',
            'organization' => 'Юридическое лицо',
            'signer' => 'Подписант',
            'prepayment_percentage' => 'Процент предоплаты',
            'term_days' => 'Срок в днях',
            'delivery_stock' => 'Место отгрузки',
            'note' => 'Примечание',
            'attachments_pages_ids' => 'Дополнительные листы',
            'promos_filenames' => 'Рекламные картинки',
            'created' => 'Дата и время создания',
        ];
    }

    public function beforeSave($insert) {
        parent::beforeSave($insert);
        $promos_filenames = !empty($this->promos_filenames) ? unserialize($this->promos_filenames) : [];

        $this->promo_2_file = UploadedFile::getInstance($this, 'promo_2_file');
        if ($this->promo_2_file) {
            $filename = $this->upload('promo_2_file');
            if ($filename) {
                $promos_filenames['2'] = $filename;
            }
        }

        $this->promo_5_file = UploadedFile::getInstance($this, 'promo_5_file');
        if ($this->promo_5_file) {
            $filename = $this->upload('promo_5_file');
            if ($filename) {
                $promos_filenames['5'] = $filename;
            }
        }

        $this->promo_8_file = UploadedFile::getInstance($this, 'promo_8_file');
        if ($this->promo_8_file) {
            $filename = $this->upload('promo_8_file');
            if ($filename) {
                $promos_filenames['8'] = $filename;
            }
        }

        if (!empty($promos_filenames)) {
            $this->promos_filenames = serialize($promos_filenames);
        }

        if (!empty($this->needs_approval_by_ids) && is_array($this->needs_approval_by_ids)) {
            $this->needs_approval_by_ids = serialize($this->needs_approval_by_ids);
        } else {
            $this->needs_approval_by_ids = null;
        }

        if (!empty($this->attachments_pages_ids) && is_array($this->attachments_pages_ids)) {
            $this->attachments_pages_ids = serialize($this->attachments_pages_ids);
        } else {
            $this->attachments_pages_ids = null;
        }

        if (!empty($this->amount_greater_than)) {
            $this->amount_greater_than = str_replace(',', '.', $this->amount_greater_than);
        }

        if (!empty($this->amount_less_than)) {
            $this->amount_less_than = str_replace(',', '.', $this->amount_less_than);
        }

        if (empty($this->created)) {
            $this->created = (new \DateTime())->format('Y-m-d H:i:s');
        }

        return !$this->hasErrors();
    }

    public function getFormData() {
        if (!empty($this->amount_greater_than)) {
            $this->amount_greater_than = str_replace('.', ',', floatval($this->amount_greater_than));
        }

        if (!empty($this->amount_less_than)) {
            $this->amount_less_than = str_replace('.', ',', floatval($this->amount_less_than));
        }

        $this->needs_approval_by_ids = unserialize($this->needs_approval_by_ids);
        $this->attachments_pages_ids = unserialize($this->attachments_pages_ids);
        $this->promos_filenames = unserialize($this->promos_filenames);

        $subjects = [];
        foreach (Subject::find()->all() as $row) {
            $subjects[$row['theme_id']] = $row['name'];
        }

        $positions = [];
        foreach (Position::find()->all() as $row) {
            $positions[$row['post_id']] = $row['post_name'];
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

        return [
            'model' => $this,
            'subjects' => $subjects,
            'positions' => $positions,
            'organizations' => $organizations,
            'signers' => $signers,
            'stocks' => $stocks,
            'attachments' => $attachments,
        ];
    }

    public function getExportTemplateData()
    {
        $brand = new Brand();
        $brand->title = 'Производственно-Коммерческая Фирма "АЗавод"';
        $brand->phone = '+7 (495) 669-290-66';
        $brand->federal_phone = '8 (800) 775-29-06';
        $brand->website = 'www.azavod.ru';
        $brand->email = 'info@azavod.ru';
        $data['brand'] = $brand;

        $data['stampFilename'] = $this->organization->getStampFilename();
        $data['signFilename'] = $this->signer->getSignFilename();

        $data['goods'] = [
            [
                'order' => '1',
                'name' => 'Конструкторская подготовка техоснастки и программ',
                'unit' => 'час.',
                'quantity' => 7,
                'price' => 2800.00,
                'amount' => 19600.00,
            ],
            [
                'order' => '2',
                'name' => 'Технологическая подготовка техоснастки + юстировка',
                'unit' => 'час.',
                'quantity' => 6,
                'price' => 2800.00,
                'amount' => 16800.00,
            ],
            [
                'order' => '3',
                'name' => 'Изготовление изделия из нержавеющей стали ANSI304',
                'unit' => 'час.',
                'quantity' => 10,
                'price' => 2800.00,
                'amount' => 28000.00,
            ],
            [
                'order' => '4',
                'name' => 'Мин закупка ANSI304 1 м.кв. 15мм, 120кг.',
                'unit' => 'шт.',
                'quantity' => 0.12,
                'price' => 190000.00,
                'amount' => 22800.00,
            ],
            [
                'order' => '5',
                'name' => 'Доставка материала по Москве',
                'unit' => 'шт.',
                'quantity' => 1,
                'price' => 3000.00,
                'amount' => 3000.00,
            ],
        ];

        $data['goodsTotal'] = 0;
        foreach ($data['goods'] as $good) {
            $data['goodsTotal'] += $good['amount'];
        }

        return $data;
    }

    public function upload($field_from)
    {
        $filename = false;
        if ($this->validate()) {
            $filename = \common\helpers\UploadHelper::uploadWithUniqueFilename(
                $this->$field_from,
                Yii::getAlias('@commercial_proposals/promos/')
            );

            $this->$field_from = null;

            if (!$filename) {
                $this->addError($field_from, 'Не удалось загрузить файл');
            }
        }

        return $filename;
    }

    public function getSigner()
    {
        return $this->hasOne(Signer::className(), ['signatory_id' => 'signer_id']);
    }

    public function setSigner($value)
    {
        $this->signer_id = $value;
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

    public function getDelivery_stock() {
        return $this->hasOne(Stock::className(), ['stock_id' => 'delivery_stock_id']);
    }

    public function setDelivery_stock($value)
    {
        $this->delivery_stock_id = $value;
    }
}
