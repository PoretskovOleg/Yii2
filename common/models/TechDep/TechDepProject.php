<?php

namespace common\models\TechDep;

use Yii;
use common\models\User;
use common\models\Old\Order;
use common\models\Old\Good;

class TechDepProject extends \yii\db\ActiveRecord
{
    public $projectFiles;
    public $priorityStart;

    public static function tableName()
    {
        return 'tech_dep_project';
    }

    public function rules()
    {
        return [
            [['type', 'priority', 'difficulty', 'orderNumber', 'dedline'], 'required', 'on' => 'form'],
            [['difficulty', 'timeStart', 'responsible'], 'required', 'on' => 'planning'],
            ['changes', 'required', 'when' => function($model) { return $model->type == 2; },
            'whenClient' => "function (attribute, value) {return $('select#techdepproject-type').val() == 2;}", 'on' => 'form'],
            ['goodId', 'required', 'when' => function($model) { return in_array($model->type, [1, 2, 3]); },
            'whenClient' => "function (attribute, value) {return $('select#techdepproject-type').val() > 0 && $('select#techdepproject-type').val() < 4;}", 'on' => 'form'],
            ['goodName', 'required', 'when' => function($model) { return $model->type == 4; },
            'whenClient' => "function (attribute, value) {return $('select#techdepproject-type').val() == 4;}", 'on' => 'form'],
            [['id','createdAt', 'readyWork', 'inWork', 'timeApproved', 'archive', 'authorId', 'type', 'status', 'priority', 'difficulty', 'responsible', 'approved'], 'integer'],
            [['notice', 'changes', 'orderNumber', 'goodName'], 'string', 'max' => 255],
            [['notice', 'changes', 'orderNumber', 'goodName'], 'trim'],
            [['dedline', 'timeStart'], 'safe'],
            [['priority'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepPriorityProject::className(), 'targetAttribute' => ['priority' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepStatusProject::className(), 'targetAttribute' => ['status' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepTypeProject::className(), 'targetAttribute' => ['type' => 'id']],
            [['projectFiles'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 5, 'maxSize' => 31457280],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№ проекта',
            'dedline' => 'Дедлайн',
            'timeStart' => 'Дата начала',
            'orderNumber' => 'Номер заказа',
            'goodId' => 'Изделие',
            'goodName' => 'Изделие',
            'type' => 'Тип проекта',
            'status' => 'Статус',
            'priority' => 'Приоритет',
            'difficulty' => 'Сложность',
            'responsible' => 'Ответственный',
            'notice' => 'Важные примечания',
            'changes' => 'Изменения',
            'projectFiles' => 'Прикрепить файлы'
        ];
    }

    public function beforeValidate()
    {
        if ($this->type != 4) {
            $orderNum = explode('-', $this->orderNumber);
            if ($orderNum[0] === 'T' || $orderNum[0] === 'Т') $this->orderNumber = 'T-'.$orderNum[1];
            elseif ($orderNum[0] === 'С' || $orderNum[0] === 'C') $this->orderNumber = 'C-'.$orderNum[1];
            else $this->orderNumber = $orderNum[0];
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        $this->dedline = (!empty($this->dedline) && !is_numeric($this->dedline)) ? 
            date_create_from_format('d.m.Y H:i:s', $this->dedline.' 23:59:59')->format('U') : $this->dedline;

        $this->timeStart = (!empty($this->timeStart) && !is_numeric($this->timeStart)) ? 
            date_create_from_format('d.m.Y H:i:s', $this->timeStart.' 00:00:01')->format('U') : $this->timeStart;

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            $changes = array();
            foreach ($changedAttributes as $attribute => $value) {
                if ($this->$attribute != $value && !empty($this->attributeLabels()[$attribute])) $changes[] = $this->attributeLabels()[$attribute];
            }
            if (!empty($changes)) {
                $modelHistory = new TechDepHistoryProject();
                $modelHistory->project = $this->id;
                $modelHistory->createdAt = strtotime('now');
                $modelHistory->author = Yii::$app->user->identity->user_id;
                $modelHistory->status = $this->status;
                $modelHistory->comment = 'Внесены изменения в проект: ' . implode(', ', $changes);
                $modelHistory->save();
            }
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    public function upload()
    {
        if ($this->validate()) { 
            foreach ($this->projectFiles as $file) {
                $name = $this->id . '_' . $this->translitFileName($file->baseName) . '.' . $file->extension;
                if (\yii\helpers\FileHelper::createDirectory('files/tech-dep-files', $mode = 0775, $recursive = true)
                    && $file->saveAs('files/tech-dep-files/' . $name)) {
                        $model = new TechDepFile();
                        $model->project = $this->id;
                        $model->name = $name;
                        if ($model->save()) {
                            $modelHistory = new TechDepHistoryProject();
                            $modelHistory->project = $this->id;
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

    public function getDifficultyProject()
    {
        return $this->hasOne(TechDepDifficulty::className(), ['id' => 'difficulty']);
    }

    public function getPriorityProject()
    {
        return $this->hasOne(TechDepPriorityProject::className(), ['id' => 'priority']);
    }

    public function getStatusProject()
    {
        return $this->hasOne(TechDepStatusProject::className(), ['id' => 'status']);
    }

    public function getTypeProject()
    {
        return $this->hasOne(TechDepTypeProject::className(), ['id' => 'type']);
    }

    public function getAuthorProject()
    {
        return $this->hasOne(User::className(), ['user_id' => 'authorId'])
            ->select(['user.last_name', 'user.first_name', 'user.patronymic']);
    }

    public function getResponsibleProject()
    {
        return $this->hasOne(User::className(), ['user_id' => 'responsible'])
            ->select(['user.last_name', 'user.first_name', 'user.patronymic']);
    }

    public function getApprovedProject()
    {
        return $this->hasOne(User::className(), ['user_id' => 'approved'])
            ->select(['user.last_name', 'user.first_name', 'user.patronymic']);
    }

    public function getOrderProject()
    {
        return $this->hasOne(Order::className(), ['contract_number' => 'orderNumber']);
    }

    public function getGoodProject()
    {
        $order = Order::find()->where(['contract_number' => $this->orderNumber])->limit(1)->one();
        if (empty($order) && is_numeric($this->orderNumber)) 
            $order = Order::find()->where(['and', ['order_number' => $this->orderNumber], ['contract_number' => '']])->limit(1)->one();

        switch ($order->order_type) {
            case '0':
                return $this->hasOne(OrderSiz::className(), ['order_siz_id' => 'goodId']);
                break;
            case '1':
            return $this->hasOne(OrderLab::className(), ['order_lab_id' => 'goodId']);
                break;
            case '2':
                return $this->hasOne(Good::className(), ['goods_id' => 'goodId']);
                break;
        }
    }

    public function getStagesProject()
    {
        return $this->hasMany(TechDepPlanning::className(), ['project' => 'id'])
            ->indexBy('stage');
    }

    public function getCommentsProject()
    {
        return $this->hasMany(TechDepComment::className(), ['project' => 'id'])
            ->orderBy('createdAt DESC');
    }
}
