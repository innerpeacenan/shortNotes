<?php
return [
    'start'=>[
    ],
    'db'=>[
        'class' => 'PDO',
        'params'=>[
            'mysql:host=localhost;dbname=notes_11',
            'root',
            1111111,
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true
            ]
        ]
    ]
];