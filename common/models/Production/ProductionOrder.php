<?php

namespace common\models\Production;

use Yii;
use common\models\User;
use common\models\Old\Good;
use common\models\Old\Order;
use common\models\Old\OrderSiz;
use common\models\Old\OrderLab;

class ProductionOrder extends \yii\db\ActiveRecord
{
    public $isPlanning;
    public $orderFiles;

    public static function tableName()
    {
        return 'production_order';
    }

    public function rules()
    {
        return [
            [
                [
                    'theme', 'priority', 'dedline'
                ], 'required'
            ],
            [
                'order', 'required', 'when' => function($model) { return $model->target == 1; },
                'whenClient' => "function (attribute, value) {return $('.production-order-form input[type=\"radio\"]:checked').val() == 1;}", 'on' => 'create'
            ],
            [
                ['nameGood', 'countStock'], 'required', 'when' => function($model) { return $model->target == 3; },
                'whenClient' => "function (attribute, value) {return $('.production-order-form input[type=\"radio\"]:checked').val() == 3;}", 'on' => 'create'
            ],
            [
                'responsible', 'required', 'when' => function($model) { return $model->isPlanning == 1; },
                'whenClient' => "function (attribute, value) {return $('.production-order-form input#productionorder-isplanning').val() == 1;}"
            ],
            [
                [
                    'createdAt', 'finishedAt', 'author', 'good', 'priority', 'target', 'theme',
                    'typeGood', 'stage', 'status', 'responsible',
                    'otk', 'countOrder', 'countStock', 'posSection'
                ], 'integer'
            ],
            [
                [
                    'number', 'nameGood', 'order', 'notice', 'section'
                ], 'string', 'max' => 255
            ],
            ['dedline', 'safe'],
            [['orderFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 2, 'maxSize' => 5242880],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionStatusOrder::className(), 'targetAttribute' => ['status' => 'id']],
            [['priority'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionPriority::className(), 'targetAttribute' => ['priority' => 'id']],
            [['target'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionTarget::className(), 'targetAttribute' => ['target' => 'id']],
            [['theme'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionTheme::className(), 'targetAttribute' => ['theme' => 'id']],
            [['typeGood'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionTypeGood::className(), 'targetAttribute' => ['typeGood' => 'id']],
            [['stage'], 'exist', 'skipOnError' => true, 'targetClass' => ProductionStageOrder::className(), 'targetAttribute' => ['stage' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'createdAt' => 'Created At',
            'good' => 'Good',
            'nameGood' => 'Наименование',
            'order' => 'Номер заказа',
            'priority' => 'Приоритет',
            'target' => 'Назначение',
            'theme' => 'Тема З/Н',
            'typeGood' => 'Тип изделия',
            'stage' => 'Этап',
            'status' => 'Status',
            'responsible' => 'Ответственный',
            'otk' => 'Исполнитель ОТК',
            'dedline' => 'Дедлайн',
            'countOrder' => 'Количество по заказу',
            'countStock' => 'Количество на склад',
            'notice' => 'Примечание для производства',
        ];
    }

    public function beforeSave($insert)
    {
        $this->dedline = (!empty($this->dedline) && !is_numeric($this->dedline)) ? 
            date_create_from_format('d.m.Y H:i:s', $this->dedline.' 23:59:59')->format('U') : $this->dedline;

        if (!empty($this->order) && !is_numeric($this->order)) $this->order = strtr($this->order, ['С' => 'C', 'Т' => 'T']);

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            $changes = array();
            foreach ($changedAttributes as $attribute => $value) {
                if (
                    in_array($attribute, ['theme', 'priority', 'notice', 'countOrder', 'countStock', 'typeGood', 'dedline']) &&
                    $this->$attribute != $value &&
                    !empty($this->attributeLabels()[$attribute])
                )
                    $changes[] = $this->attributeLabels()[$attribute];
            }
            if (!empty($changes)) {
                $modelHistory = new ProductionOrderHistory();
                $modelHistory->order = $this->id;
                $modelHistory->createdAt = strtotime('now');
                $modelHistory->author = Yii::$app->user->identity->user_id;
                $modelHistory->status = $this->status;
                $modelHistory->comment = 'Внесены изменения в заказ-наряд: ' . implode(', ', $changes);
                $modelHistory->save();
            }
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    public function upload()
    {
        if ($this->validate()) { 
            foreach ($this->orderFiles as $file) {
                $name = $this->id . '_' . $this->translitFileName($file->baseName) . '.' . $file->extension;
                if (\yii\helpers\FileHelper::createDirectory('files/production-order-files', $mode = 0775, $recursive = true)
                    && $file->saveAs('files/production-order-files/' . $name)) {
                        $model = new ProductionOrderFile();
                        $model->order = $this->id;
                        $model->stage = $this->stage;
                        $model->name = $name;
                        if ($model->save()) {
                            $modelHistory = new ProductionOrderHistory();
                            $modelHistory->order = $this->id;
                            $modelHistory->createdAt = strtotime('now');
                            $modelHistory->author = Yii::$app->user->identity->user_id;
                            $modelHistory->status = $this->status;
                            $modelHistory->comment = 'Добавлен файл: ' . $name;
                            $modelHistory->save();
                        }
                    }
            }
            return true;
        } else {
            return false;
        }
    }

    public function translitFileName($string) {
      $translit = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
        'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
        'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ы' => 'y', 'ъ' => '', 'ь' => '', 'э' => 'eh', 'ю' => 'yu', 'я'=>'ya');

      return str_replace(' ', '_', strtr(mb_strtolower(trim($string)), $translit));
    }

    public function getStatusOrder()
    {
        return $this->hasOne(ProductionStatusOrder::className(), ['id' => 'status']);
    }

    public function getPriorityOrder()
    {
        return $this->hasOne(ProductionPriority::className(), ['id' => 'priority']);
    }

    public function getTargetOrder()
    {
        return $this->hasOne(ProductionTarget::className(), ['id' => 'target']);
    }

    public function getThemeOrder()
    {
        return $this->hasOne(ProductionTheme::className(), ['id' => 'theme']);
    }

    public function getTypeGoodOrder()
    {
        return $this->hasOne(ProductionTypeGood::className(), ['id' => 'typeGood']);
    }

    public function getStageOrder()
    {
        return $this->hasOne(ProductionStageOrder::className(), ['id' => 'stage']);
    }

    public function getResponsibleOrder()
    {
        return $this->hasOne(User::className(), ['user_id' => 'responsible']);
    }

    public function getOtkOrder()
    {
        return $this->hasOne(User::className(), ['user_id' => 'otk']);
    }

    public function getGoodOrder()
    {
        $order = Order::find()->where(['contract_number' => $this->order])->limit(1)->one();
        if (empty($order) && is_numeric($this->order)) 
            $order = Order::find()->where(['and', ['order_number' => $this->order], ['contract_number' => '']])->limit(1)->one();

        switch ($order->order_type) {
            case '0':
                return $this->hasOne(OrderSiz::className(), ['order_siz_id' => 'good']);
                break;
            case '1':
            return $this->hasOne(OrderLab::className(), ['order_lab_id' => 'good']);
                break;
            case '2':
                return $this->hasOne(Good::className(), ['goods_id' => 'good']);
                break;
        }
    }

    public function getCommentsOrder()
    {
        return $this->hasMany(ProductionOrderComment::className(), ['order' => 'id'])
            ->orderBy('createdAt DESC');
    }

    public function getIdOrder()
    {
        return $this->hasOne(Order::className(), ['contract_number' => 'order'])
            ->orWhere(['and', ['order_number' => 'order'], ['contract_number' => '']]);
    }
}
