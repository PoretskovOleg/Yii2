<?php

namespace common\models;

use Yii;

class Attachment extends \yii\db\ActiveRecord
{
    const TYPE_IMAGE = 1;
    const TYPE_PDF = 2;

    public $file;

    public static function tableName()
    {
        return 'attachments';
    }

    public function rules()
    {
        return [
            [['name'], 'required', 'message' => 'Поле не может быть пустым'],
            [['file'],
                'required',
                'on' => 'create',
                'except' => 'validated',
                'message' => 'Поле не может быть пустым',
            ],
            [['file'],
                'file',
                'extensions' => 'jpg, jpeg, png, pdf',
                'wrongExtension' => 'Файл имеет недопустимый тип',
            ],
            [['name', 'filename'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '№',
            'name' => 'Название',
            'filename' => 'Файл',
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->setScenario('validated');
            $this->filename = \common\helpers\UploadHelper::uploadWithUniqueFilename($this->file,
                Yii::getAlias('@attachments')
            );

            if (($this->file->extension == 'jpg') || ($this->file->extension == 'png') || ($this->file->extension == 'jpeg')) {
                $this->type = self::TYPE_IMAGE;
            } elseif (($this->file->extension == 'pdf')) {
                $this->type = self::TYPE_PDF;
            }

            $this->file = null;

            if (!$this->filename) {
                $this->addError('file', 'Не удалось загрузить файл');
            }
        }
    }
}
