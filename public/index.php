<?php
defined('N_APPLICATION') or define('N_APPLICATION', __DIR__ . '/..');
defined('N_DEBUG') or define('N_DEBUG', false);
defined('N_TEST') or define('N_TEST', false);

// enable error reportings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_REQUEST['test'])) {
    $ini = require(__DIR__ . '/../config/debug.php');
}
$ini = require(__DIR__ . '/../config/undebug.php');
$ini = require(__DIR__ . '/../config/main.php');
require(__DIR__ . '/../libary/nxn/Autoload.php');
require(__DIR__ . '/../libary/nxn/N.php');
(new nxn\web\Application($ini))->run();
?>
