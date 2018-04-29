<?php
// set up env
$envirementVariables = require(__DIR__ . '/../.env');
if(!is_array($envirementVariables)){
     throw new \Exception("env file should return an array");
}

foreach($envirementVariables as $name => $value){
   putenv($name . '=' . $value);
}

// declare constant
defined('N_APPLICATION') or define('N_APPLICATION', __DIR__ . '/..');

require(__DIR__ . '/../config/helper.php');

$ini = require(__DIR__ . '/../config/main.php');

require(__DIR__ .'/../vendor/autoload.php');

require(__DIR__ . '/../libary/nxn/N.php');

// 旧的自动加载类作废
//require(__DIR__ . '/../libary/nxn/Autoload.php');

(new nxn\web\Application($ini))->run();
