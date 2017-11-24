<?php

namespace frontend\widgets;

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Url;

class HtmlEditor extends CKEditor
{
    public function init() {
        $this->preset = 'basic';
        $this->clientOptions = [
            'language' => 'ru',
            'extraPlugins' => 'colorbutton',
            'filebrowserUploadUrl' => Url::toRoute('commercial-proposal/upload-image'),
            'toolbar' => [
                ['name' => 'main', 'items' => [
                    'Bold',
                    'Italic',
                    'Underline',
                    'Strike',
                    '-',
                    'RemoveFormat',
                    '-',
                    'NumberedList',
                    'BulletedList',
                    'Blockquote',
                    '-',
                    'TextColor',
                    'BGColor',
                    '-',
                    'Image',
                    '-',
                    'Link',
                    'Unlink',
                ]],
            ]
        ];
        parent::init();
    }
}