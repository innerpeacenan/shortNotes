
<?php
require_once  '/home/wwwroot/www.note.com/vendor/composer/autoload_real.php';
ComposerAutoloaderInit0fb7274d6961f08116ce84af67e50509::getLoader();
$_SERVER['REQUEST_URI'] = '/';
//echo phpinfo();
// define Constants
defined('N_TEST') or define('N_TEST',true);
// Use of undefined constant N_DEBUG - assumed 'N_DEBUG'
defined('N_DEBUG') or define('N_DEBUG',true);
defined('N_APPLICATION') or define('N_APPLICATION',__DIR__  .  '/..');
// enable debuging infomation
if(N_DEBUG){
    // enable error reportings
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(E_ALL);
}

// test if deug work setting works,ok ,ite works
// set auto_load
$ini = require (__DIR__ .'/../config/main.php');
require (__DIR__ .'/../libary/nxn/Autoload.php' );
require (__DIR__ .'/../libary/nxn/N.php' );
(new nxn\web\Application($ini))->run();

//http://www.note.com/css/bootstrap.css.map  这个静态资源没有找到
// @todo 写了非常简单的依赖注入,接下来进一步优化
// 主要存在的问题:@ 检查不够,规则不统一
//var_dump($app);
?>
