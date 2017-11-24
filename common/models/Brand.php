<?php

namespace common\models;

use Yii;

class Brand extends \yii\db\ActiveRecord
{
    public $logo_file;

    public static function tableName()
    {
        return 'brands';
    }

    public function rules()
    {
        return [
            [['title'], 'required', 'message' => 'Поле не может быть пустым'],
            [['slogan', 'address'], 'string'],
            [['title', 'city', 'phone', 'federal_phone', 'website', 'email', 'logo_filename'], 'string', 'max' => 255],
            [
                ['logo_file'],
                'file',
                'except' => 'validated',
                'maxSize' => 2 * 1024 * 1024,
                'tooBig' => 'Файл cлишком большой',
                'extensions' => 'jpg, jpeg, png',
                'wrongExtension' => 'Файл имеет недопустимый тип',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№',
            'title' => 'Название бренда',
            'slogan' => 'Девиз',
            'city' => 'Город',
            'address' => 'Адрес',
            'phone' => 'Телефон',
            'federal_phone' => 'Телефон 8-800',
            'website' => 'Сайт',
            'email' => 'Email',
            'logo_filename' => 'Логотип',
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->setScenario('validated');
            $this->logo_filename = \common\helpers\UploadHelper::uploadWithUniqueFilename($this->logo_file,
                Yii::getAlias('@brands/logos')
            );
        }

        if (!$this->logo_filename) {
            $this->addError('logo_file', 'Не удалось загрузить файл');
        }
    }
}