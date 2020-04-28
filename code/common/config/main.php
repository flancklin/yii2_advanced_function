<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2_advanced_function',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => "tp_",
        ],
        'user' => [
//            'class' =>'yii\web\user',
            'identityClass' => 'app\models\User',//可以自定义user的某些规则
        ],
    ],
];
