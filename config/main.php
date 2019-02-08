<?php
return [
    'HttpKernel' => [
        'class' => \play\web\Kernel::class,
    ],
    'router' => [
        'class' => \play\web\MatchRouter::class,
    ],
    'db' => [
        // POD 构造函数的最后一个参数从这里传入
        'shareParam' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true
        ],
        'master' => [
            [
                'class' => 'PDO',
                'params' => [
                    'mysql:host=' . getenv('DB_MASTER_HOST') . ';port=' . getenv('DB_MASTER_PORT') . ';dbname=' . getenv('DB_MASTER_NAME'),
                    getenv('DB_MASTER_USER'),
                    getenv('DB_MASTER_PASS'),
                ]
            ],
        ],
        'slave' => [
            [
                'class' => 'PDO',
                'params' => [
                    'mysql:host=' . getenv('DB_MASTER_HOST') . ';port=' . getenv('DB_MASTER_PORT') . ';dbname=' . getenv('DB_MASTER_NAME'),
                    getenv('DB_MASTER_USER'),
                    getenv('DB_MASTER_PASS'),
                ]
            ],
        ]
    ],
    'session' => [
        'driver' => getenv('SESSION_DRIVER'),
        'name' => getenv('SESSION_VALUE'),
    ],
    'redis' => [
        'ip' => getenv('REDIS_HOST'),
        'port' => getenv('REDIS_PORT'),
    ]
];
