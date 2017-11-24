<?php
return [
    'language' => 'ru',
    'charset' => 'UTF-8',
    'timeZone' => 'Europe/Moscow',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=new_ienergo',
            'username' => 'new_ienergo',
            'password' => 'sR6o7wHoxfv6kJh2PjT5',
            'charset' => 'utf8',
        ],
        'old_db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=el_master',
            'username' => 'new_ienergo',
            'password' => 'sR6o7wHoxfv6kJh2PjT5',
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
