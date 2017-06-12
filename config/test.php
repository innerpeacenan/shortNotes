<?php
return [
//    PHPUnit_Framework_Exception: PHP Fatal error:  Uncaught PDOException: You cannot serialize or unserialize PDO instances in -:336
// 当以多进程的方式跨库操作的时候,会报上面的错误
    'db' => [
        'class' => 'PDO',
        'params' => [
            'mysql:host=localhost;dbname=notes_test',
            'root',
            1111111,
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_PERSISTENT => true
            ]
        ]
    ],
//    暂时无法投入使用
    'stubdb' => [
        'class' => 'PDO',
        'params' => [
            'mysql:host=localhost;dbname=notes_test',
            'root',
            1111111,
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_PERSISTENT => true
            ]
        ]
    ],

];