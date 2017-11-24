<?php

namespace common\models\Invoice;

use yii\base\Model;
use yii\web\UploadedFile;


class InvoiceEmail extends Model
{
    public $email;
    public $subject;
    public $text;
    public $attachment;
    public $invoice_id;
    public $invoice_filename;

    public function rules()
    {
        return [
            [['email', 'subject', 'text', 'invoice_filename'], 'required'],
            [['email'], 'email'],
            [['email', 'subject', 'invoice_filename'], 'string', 'max' => 255],
            [['text'], 'string'],
            [['attachment'], 'file', 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels() {
        return [
            'email' => 'Email',
            'subject' => 'Тема',
            'text' => 'Текст',
            'invoice_filename' => 'Имя файла',
            'attachment' => 'Дополнительный файл',
        ];
    }

    public function send($pdf)
    {
        $invoice = Invoice::findOne(\Yii::$app->request->post('invoice_id'));
        $invoice_attachments = $invoice->files;

        $username = 'info@lab-electro.ru';
        $password = 'pass44@369';
        if (\Yii::$app->user->identity->work_email == 'zakaz@elektrik-master.ru') {
            $username = 'zakaz@elektrik-master.ru';
            $password = 'pass44@147';
        } elseif (\Yii::$app->user->identity->work_email == 'info@azavod.ru') {
            $username = 'info@azavod.ru';
            $password = 'info6567';
        } elseif (!empty(\Yii::$app->user->identity->work_email) && !empty(\Yii::$app->user->identity->work_email_password)) {
            $username = \Yii::$app->user->identity->work_email;
            $password = \Yii::$app->user->identity->work_email_password;
        }

        $transport = new \Swift_SmtpTransport();
        $transport->setHost('smtp.yandex.ru');
        $transport->setUsername($username);
        $transport->setPassword($password);
        $transport->setPort(465);
        $transport->setEncryption('ssl');
        $mailer = new \yii\swiftmailer\Mailer();
        $mailer->setTransport($transport);

        try {
            $message = $mailer->compose()
                ->setFrom($username)
                ->setTo($this->email)
                ->setSubject($this->subject)
                ->setTextBody($this->text)
                ->attachContent($pdf, ['fileName' => $this->invoice_filename, 'contentType' => 'application/pdf']);

            foreach ($invoice_attachments as $attachment) {
                $message->attach(\Yii::getAlias('@webroot') . \Yii::getAlias('@invoices_attachments/') . $attachment->filename);
            }

            $this->attachment = UploadedFile::getInstance($this, 'attachment');
            if (!empty($this->attachment)) {
                $path = \Yii::getAlias('@webroot') . \Yii::getAlias('@invoices_temp');
                if (\yii\helpers\FileHelper::createDirectory($path, $mode = 0775, $recursive = true)) {
                    $this->attachment->saveAs($path . '/' . $this->attachment->baseName . '.' . $this->attachment->extension);
                    $message->attach($path . '/' . $this->attachment->baseName . '.' . $this->attachment->extension);
                } else {
                    return false;
                }
            }

            return $mailer->send($message);
        } catch (\Exception $e) {
            return false;
        }
    }
}
