<?php
namespace tests;
use tests\lib\ArrayDataSets;
use yii\base\Exception;

/**
 * @todo 目前还很不健全,需要进一步优化, 但是我还没有想好怎么做
 * @package nxn\web
 * User: xiaoning nan
 * Date: 2017-05-{13}
 * Time: xx:xx
 * Description: description
 */
class Controller extends ArrayDataSets
{
    /**
     * @var null|string
    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     * 根据当前的控制器,解析出原始请求的路由
     *
     */
    public function initRequest()
    {
//tests\modules\index\Note\DeleteNotePostTest
        $str = get_called_class();
        $baseNameSpace = 'tests\\modules\\';
        if ((strncmp($str, $baseNameSpace, strlen($baseNameSpace)))) {
            throw new Exception("can not get ruote from class name,error class name space?");
        } else {
            $str = substr($str, strlen($baseNameSpace));
        }
        $str = preg_split('/,/', $str, -1, PREG_SPLIT_NO_EMPTY);
        $action = array_pop($str);
        $methods = ['POST', 'GET', 'DELETE', 'PUT', 'HEAD', 'OPTION', 'CONNECTION'];
        foreach ($methods as $method) {
            if (($pos = stripos($action, $method)) !== false) {
                $_SERVER['REQUEST_METHOD'] = $method;
                // it is camelcased
                $action = substr($action, 0, -$pos);
                $action = preg_replace('/(([a-z])[A-Z])/', '${1}-${2}', $action);
                if (!is_string($action)) {
                    throw new Exception('error regular expression, or some other error');
                } else {
                    $action = strtolower($action);
                }
//                preg_split('/([A-Z])/', $ta, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
//        [$c[2],preg_split('/(?=[A-Z])/', substr($c[3],0,-10), -1, PREG_SPLIT_NO_EMPTY),$c[4]];
            }
        }
        $controller = array_pop($str);
        $controller = preg_replace('/(([a-z])[A-Z])/', '${1}-${2}', $controller);
        if (!is_string($controller)) {
            throw new Exception(var_dump($controller));
        }
        $controller = strtolower($controller);
        $module = $str;
        $module = preg_replace('/(([a-z])[A-Z])/', '${1}-${2}', $module);
        if (!is_string($module)) {
            throw new Exception(var_dump($module));
        }
        $module = strtolower($module);
        $_SERVER['REQUEST_URL'] = join('/', [$module, $controller . $action]);
        $ini = require(__DIR__ . '/../../config/test.php');
        // 约定一个全局变量,用于测试返回的数据
        (new \nxn\web\Application($ini))->run();
    }
}
