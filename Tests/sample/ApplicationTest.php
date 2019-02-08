<?php

namespace Test\sample;

use play\web\Application;
use PHPUnit\Framework\TestCase;
use \PDO;

/**
 * Class ApplicationTest
 * @runInSeparateProcess
 */
class ApplicationTest extends TestCase
{
    /**
     * @var Application
     */
    public $app;

    public function setUp()
    {
        $config = [
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

        $this->app = new Application($config);
        parent::setUp();
    }

    public function testSetRouter()
    {
        $_SERVER['REQUEST_URI'] = '/index/index';
        $_SERVER['REQUEST_METHOD'] = "GET";
        $this->app->setRouter();
        $this->assertEquals([
            'indexGET',
            'IndexController',
            'n\\modules\\index\\controllers\\',
        ], [
            $this->app->router['action'],
            $this->app->router['controller'],
            $this->app->router['module']
        ]);
    }

    public function testSetRouterWithLongModule()
    {
        $_SERVER['REQUEST_URI'] = '/item/get-items';
        $_SERVER['REQUEST_METHOD'] = "GET";
        $this->app->setRouter();
        $this->assertEquals([
            'getItemsGET',
            'ItemController',
            'n\\modules\\index\\controllers\\',
        ], [
            $this->app->router['action'],
            $this->app->router['controller'],
            $this->app->router['module']
        ]);
    }

    public function testSetRouterWithOnlySingleSlash()
    {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = "GET";
        $this->app->setRouter();
        $this->assertEquals([
            'indexGET',
            'IndexController',
            'n\\modules\\index\\controllers\\',
        ], [
            $this->app->router['action'],
            $this->app->router['controller'],
            $this->app->router['module']
        ]);
    }

    public function testSetRouterWithEmptyRequestUri()
    {
        $_SERVER['REQUEST_URI'] = '';
        $_SERVER['REQUEST_METHOD'] = "GET";
        $this->app->runAction();
        $class = 'n\\modules\\index\\controllers\\IndexController';
        $this->assertTrue(class_exists($class));
        // PHPUnit_Framework_Exception: Argument #1 (No Value) of PHPUnit_Framework_Assert::assertInstanceOf() must be a class or interface name
        if (class_exists($class)) {
            $this->assertInstanceOf($class, $this->app->controller);
        }
    }


    public function testRunAction()
    {
        $_SERVER['REQUEST_URI'] = '';
        $_SERVER['REQUEST_METHOD'] = "GET";
        $this->app->setRouter();
        $this->assertEquals([
            'indexGET',
            'IndexController',
            'n\\modules\\index\\controllers\\',
        ], [
            $this->app->router['action'],
            $this->app->router['controller'],
            $this->app->router['module']
        ]);
    }


    public function testGetter()
    {
        $app = $this->app;
        $db = $app->stubdb;
        $this->assertInstanceOf('\\PDO', $db);
        $r = new  \ReflectionObject($app->container);
        $p = $r->getProperty('_singletons');
        $p->setAccessible(true); // <--- you set the property to public before you read the value
        // bug fix history:ReflectionProperty::getValue() expects exactly 1 parameter, 0 given
//    ReflectionException: Cannot access non-public member play\di\Container::_singletons
//      why null?
        $singletons = $p->getValue($app->container);
        $this->assertInternalType('array', $singletons);
        $this->assertArrayHasKey('stubdb', $singletons);
    }

    public function testGetDb()
    {
        $app = $this->app;
        $db1 = $app->getDb();
        $db2 = $app->getDb();
//  检查两个对象是否具有相同的引用源, 也就是说看两个变量所引用的对象是否是同一个单例对象
        self::assertTrue($db1 === $db2);
    }
}
