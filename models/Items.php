<?php
namespace n\models;

use nxn\db\ActiveRecord;

class Items extends ActiveRecord
{
    public static function tableName()
    {
        return 'items';
    }


    public function getOldAttribute()
    {
        return $this->_oldAttributes;
    }

    /**
     * @access
     * @param object $behavior
     * @return void
     *
     * Description:
     * 1 passed to n\models\Items::setBehavior() must be an instance of n\models\object,
     * 如果需要对象,最好不要在接口模糊申明为 object, 匿名类不是继承字 \object 对象,在参数接口种申明值类型必须为 \object
     *
     *
     */
    public function setBehavior($behavior)
    {
        var_export($behavior);
    }

    public function testInvoice()
    {
        $dataSet = new
        \PHPUnit_Extensions_Database_DataSet_QueryDataSet
        ($this->getConnection());
        $dataSet->addTable('customer');
        $dataSet->addTable('product');
        $dataSet->addTable('invoice');
        $dataSet->addTable('invoice_item');
        $dataSet->addTable('invoice_status');
        $expectedDataSet = $this->getDataSet();
        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public static function select()
    {
        $db = new \PDO('mysql:host=localhost;dbname=test', 'root', '1111111');
        $st = $db->prepare("select * from customer where customer_id =:customer_id");
        $st->execute(array('customer_id' => 2));
        $result = $st->fetch();
        return $result;
    }

    /**
     * @param string | array $primaryKey
     * @return void
     *
     */
    public static function setPrimaryKey($primaryKey)
    {
        if (is_string($primaryKey)) {
            $primaryKey = [$primaryKey];
        } elseif (!is_array($primaryKey)) {
            throw new \Exception('primary key must be an array or string!');
        }
        // 采用匿名类以后, 这里不能再用 static 或者 self 关键字了,因为类里边的 self 指的是外边的这个类
        Items::$_primaryKey = $primaryKey;
    }

    public function getPrimaryKey()
    {
        return static::$_primaryKey;
    }

    public function getAttribute()
    {
        return $this->_attributes;
    }
}