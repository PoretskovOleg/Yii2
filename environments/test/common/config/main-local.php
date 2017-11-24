<?php
return [
    'language' => 'ru',
    'charset' => 'UTF-8',
    'timeZone' => 'Europe/Moscow',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=test_db',
            'username' => '2015.admin88.ru',
            'password' => '3TXLTVmqPFRsUPDc',
            'charset' => 'utf8',
        ],
        'old_db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=test_old_db',
            'username' => '2015.admin88.ru',
            'password' => '3TXLTVmqPFRsUPDc',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
        ],
    ],
];
