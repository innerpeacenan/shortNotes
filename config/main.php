<?php
return [
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
                    'mysql:host=localhost;dbname=notes',
                    'root',
                    1111111,
                ]
            ],
        ],
        'slave' => [
            [
                'class' => 'PDO',
                'params' => [
                    'mysql:host=localhost;dbname=notes',
                    'root',
                    1111111,
                ],
            ]
        ]
    ],
    'session' => [
        'driver' => getenv('SESSION_DRIVER'),
        'name' => 'makeLifeEasier',
    ],

    'redis' => [
        'ip' => '127.0.0.1',
        'port' => '6379',
    ]
];
