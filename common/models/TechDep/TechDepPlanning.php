<?php

namespace common\models\TechDep;

use Yii;
use common\models\User;

class TechDepPlanning extends \yii\db\ActiveRecord
{
    public $typeFile_1;
    public $typeFile_2;
    public $typeFile_3;
    public $typeFile_4;
    public $typeFile_5;
    public $typeFile_6;
    public $typeFile_7;
    public $typeFile_8;
    public $typeFile_9;
    public $typeFile_10;
    public $typeFile_11;
    public $typeFile_12;
    public $typeFile_13;
    public $typeFile_14;
    public $typeFile_15;
    public $typeFile_16;
    public $typeFile_17;
    public $typeFile_18;
    public $typeFile_19;

    public static function tableName()
    {
        return 'tech_dep_planning';
    }

    public function rules()
    {
        return [
            [['project', 'stage', 'status', 'dedlineTime', 'pureTime', 'contractor'], 'integer'],
            [['stage'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepStagesProject::className(), 'targetAttribute' => ['stage' => 'id']],
            [['project'], 'exist', 'skipOnError' => true, 'targetClass' => TechDepProject::className(), 'targetAttribute' => ['project' => 'id']],
            [
                [
                    'typeFile_1', 'typeFile_2', 'typeFile_3', 'typeFile_4', 'typeFile_5', 'typeFile_6',
                    'typeFile_7', 'typeFile_8', 'typeFile_9', 'typeFile_10', 'typeFile_11', 'typeFile_12',
                    'typeFile_13', 'typeFile_14', 'typeFile_15', 'typeFile_16', 'typeFile_17', 'typeFile_18', 'typeFile_19'
                ], 'file', 'skipOnEmpty' => true, 'maxFiles' => 5, 'maxSize' => 31457280
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project' => 'Project',
            'stage' => 'Stage',
            'dedlineTime' => 'Dedline Time',
            'pureTime' => 'Pure Time',
            'contractor' => 'Contractor',
        ];
    }

    public function upload($typeFile)
    {
        if ($this->validate()) { 
            foreach ($this->$typeFile as $file) {
                $name = $this->project . '_' . $this->translitFileName($file->baseName) . '.' . $file->extension;
                if (\yii\helpers\FileHelper::createDirectory('files/tech-dep-files', $mode = 0775, $recursive = true)
                    && $file->saveAs('files/tech-dep-files/' . $name)) {
                    $model = new TechDepStageFile();
                    $model->project = $this->project;
                    $model->stage = $this->stage;
                    $model->type = explode('_', $typeFile)[1];
                    $model->name = $name;
                    if ($model->save()) {
                        $modelHistory = new TechDepHistoryStage();
                        $modelHistory->project = $this->project;
                        $modelHistory->stage = $this->stage;
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

    public function getStageProject()
    {
        return $this->hasOne(TechDepStagesProject::className(), ['id' => 'stage']);
    }

    public function getContractorStage()
    {
        return $this->hasOne(User::className(), ['user_id' => 'contractor']);
    }

    public function getStatusStage()
    {
        return $this->hasOne(TechDepStatusStage::className(), ['id' => 'status']);
    }
}
