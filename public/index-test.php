<?php
// 同意设置时区
date_default_timezone_set("Asia/Shanghai");
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

$ini = require(__DIR__ . '/../config/main.php');

require(__DIR__ .'/../vendor/autoload.php');

require(__DIR__ . '/../config/helper.php');

new \nxn\web\Application($ini);
