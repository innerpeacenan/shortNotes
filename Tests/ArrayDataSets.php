<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/6/17
 * Time: 9:13 PM
 */
namespace Tests;

/**
 * Class ArrayDataSets
 * @package Tests
 * User: xiaoning nan
 * Date: 2017-06-{06}
 * Time: xx:xx
 * Description: description
 */
abstract class ArrayDataSets extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @var \PDO $pdo
     */
    public $pdo;

    public function getConnection()
    {
        $this->pdo = $this->pdo ?? new \PDO('mysql:host=localhost;dbname=notes_test', 'root', '1111111');
        return $this->createDefaultDBConnection($this->pdo);
    }

    abstract public function dataSet();

    public function getDataSet()
    {
        $arr = $this->dataSet();
        return new \PHPUnit_Extensions_Database_DataSet_ArrayDataSet($arr);
    }

    public function setUp()
    {
        //这一行很重要,继承了父类的行为,会每次先清空数据表,在插入结果集,非常好
        parent::setUp();
        $conn = $this->getConnection();
        $this->pdo = $conn->getConnection();
        $this->pdo->query("set foreign_key_checks=0");
    }
    /**
     * @access
     * @param $obj
     * @return  mixed
     * 用于获取对象的
     */
    public function getInvisableProperty($obj, $property)
    {
        $r = new \ReflectionObject($obj);
        $p = $r->getProperty($property);
        $p->setAccessible(true); // <--- you set the property to public before you read the value
        $reult = $p->getValue($obj);
//      不要影响该类的属性的正常使用
        $p->setAccessible(false);
        return $reult;
    }


}
