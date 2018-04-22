<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/31/17
 * Time: 9:39 PM
 */
namespace tests\lib;
use n\models\Items;
/**
 * Class DbTest
 * @package tests
 * User: xiaoning nan
 * Date: 2017-05-{31}
 * Time: xx:xx
 * Description: description
 */
class DbTest extends \PHPUnit_Extensions_Database_TestCase
{
    public function getConnection()
    {
        // 这是 db unit 建桩的方法
        return $this->createDefaultDBConnection(new \PDO('mysql:host=localhost;dbname=test', 'root', '1111111'));
    }

    public function getDataSet()
    {
//        return $this->createMySQLXMLDataSet()
        return $this->createFlatXMLDataSet(N_APPLICATION . 'tests/data/dataset.xml');
    }

    protected function setUp()
    {
        $conn = $this->getConnection();
        $conn->getConnection()->query("set foreign_key_checks=0");
        parent::setUp();
        $conn->getConnection()->query("set foreign_key_checks=1");
    }

    public function testConsumer()
    {
        $result = Items::select();
        $this->assertEquals("jenny.smith@localhost",
            $result['email']);
        $dbTable = $this->getConnection()->createQueryTable(
            'customer', 'SELECT * FROM customer');
        $datasetTable = $this->getDataSet()
            ->getTable("customer");
        $this->assertTablesEqual($dbTable, $datasetTable);
    }
}
