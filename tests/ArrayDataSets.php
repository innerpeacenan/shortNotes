<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/6/17
 * Time: 9:13 PM
 */
namespace tests\lib;

/**
 * Class ArrayDataSets
 * @package tests
 * User: xiaoning nan
 * Date: 2017-06-{06}
 * Time: xx:xx
 * Description: description
 */
class ArrayDataSets extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @var \PDO $stub
     */
    public $stub;

    public function getConnection()
    {
        return $this->createDefaultDBConnection(new \PDO('mysql:host=localhost;dbname=notes_test', 'root', '1111111'));
    }

    public function dataSet()
    {
        $arr = [
            'items' => [

            ],
            'notes' => [

            ]
        ];
        return $arr;
    }

    public function getDataSet()
    {
        $arr = $this->dataSet();
        return new \PHPUnit_Extensions_Database_DataSet_ArrayDataSet($arr);
    }

    public function setUp()
    {
        //@inportent 这一行很重要,继承了父类的行为,会每次先清空数据表,在插入结果集,非常好
        parent::setUp();
        $conn = $this->getConnection();
        $this->stub = $conn->getConnection();
        $this->stub ->query("set foreign_key_checks=0");
    }
}