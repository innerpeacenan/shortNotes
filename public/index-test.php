<?php
defined('N_TEST') or define('N_TEST', true);
defined('N_DEBUG') or define('N_DEBUG', true);
defined('N_APPLICATION') or define('N_APPLICATION', __DIR__ . '/..');

require_once N_APPLICATION . '/vendor/composer/autoload_real.php';
ComposerAutoloaderInit0fb7274d6961f08116ce84af67e50509::getLoader();
if (N_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
//$_SERVER['REQUEST_URI'] = '/index/index';
require(__DIR__ . '/../libary/nxn/Autoload.php');
require(__DIR__ . '/../libary/nxn/N.php');
$ini = require(__DIR__ . '/../config/test.php');




//new nxn\web\Application($ini);
?>
