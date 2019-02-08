<?php
namespace Tests;

use Exception;
/**
 * @todo 目前还很不健全,需要进一步优化, 但是我还没有想好怎么做
 * @package play\web2
 * User: xiaoning nan
 * Date: 2017-05-{13}
 * Time: xx:xx
 * Description: description
 */
abstract class Controller extends ArrayDataSets
{
    /**
     * set gloabe variouble when need
     */
    abstract public function setRequest();

    /**
     * Description:
     * 根据当前的控制器,解析出原始请求的路由
     */
    private function initRequest()
    {
//Tests\modules\index\Note\DeleteNotePostTest
        $initRuter = get_called_class();
        $baseNameSpace = 'Tests\\modules\\';
        if ((strncmp($initRuter, $baseNameSpace, strlen($baseNameSpace)))) {
            throw new Exception("can not get ruote from class name,error class name space?");
        } else {
            $initRuter = substr($initRuter, strlen($baseNameSpace));
        }
//        error spliter \',' ,use \\ as spliter in namespace string
//        preg_split(): No ending delimiter '/' found
//        @todo 弄明白为什么需要四个反斜线
        $initRuter = preg_split('/\\\\/', $initRuter, -1, PREG_SPLIT_NO_EMPTY);
        $action = array_pop($initRuter);
        if (substr_compare($action, "Test", -4, 4) !== 0) {
            throw new Exception('Test case name must end with \'test\'');
        }
        $action = substr($action, 0, strlen($action) - 4);
//       DeleteNotePost
        $methods = ['POST', 'GET', 'DELETE', 'PUT', 'HEAD', 'OPTION', 'CONNECTION'];
        foreach ($methods as $method) {
            $len = strlen($method);
//      the forth parameter true means case insensitive
            if (substr_compare($action, $method, -$len, $len, true) === 0) {
                // it is camelcased
                $action = substr($action, 0, -$len);
                $action = preg_replace('/([a-z])([A-Z])/', '${1}-${2}', $action);
                if (!is_string($action)) {
                    throw new Exception('error regular expression, or some other error');
                } else {
                    $action = strtolower($action);
                }
                $_SERVER['REQUEST_METHOD'] = $method;
                break;
            }
        }
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            throw new Exception('invalid action path or name?');
        }
        $controller = array_pop($initRuter);
        $controller = preg_replace('/([a-z])([A-Z])/', '${1}-${2}', $controller);
        if (!is_string($controller)) {
            throw new Exception('controller should be string' . json_encode($controller));
        }
        $controller = strtolower($controller);
        $module = join('/', $initRuter);
        if (!is_string($module)) {
            throw new Exception('module should be string' . json_encode($module));
        }
        $module = strtolower($module);
//        uri Uniform Resource Identifier (URI)
        $_SERVER['REQUEST_URI'] = join('/', [$module, $controller, $action]);
        require(__DIR__ . '/../config/debug.php');
        $ini = require(__DIR__ . '/../config/test.php');
        // 约定一个全局变量,用于测试返回的数据
        (new \play\web\Application($ini))->run();
    }


    public function setUp()
    {
        parent::setUp();
        //初始化请求
        $this->setRequest();
        $this->initRequest();
    }
}
