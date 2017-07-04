<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2/17/17
 * Time: 9:28 PM
 */
namespace nxn\db;

/**
 * Class Query
 * @package nxn\db
 * User: xiaoning nan
 * Date: ${YEAR}-${MONTH}-{$DAY}
 * Time: xx:xx
 * Description: description
 * 这个类待进一步测试
 */
class Query
{
    /**
     * @var \PDO
     */
    protected static $db;

    /**
     * @return \PDO
     *
     */
    public static function getDb()
    {
        if (!isset(static::$db)) {
            static::$db = \N::$app->get('db');
        }
        return static::$db;
    }

    /**
     * @param \PDO $db
     */
    public static function setDb(\PDO $db)
    {
        self::$db = $db;
    }

    /**
     * @return string late binded class name
     */
    public static function className()
    {
        return get_called_class();
    }

    /**
     * @access
     * @param $sql
     * @param array $params
     * @return array
     */
    public static function one(string $sql, array $params = [])
    {
        if (!empty($params)) {
            // configureed to trow Exception on fail
            $st = static::getDb()->prepare($sql);
            $binded = static::bindParam($st, $sql, $params);
            if (false === $binded) {
                return [];
            }
        } else {
            $st = static::getDb()->query($sql);
        }
        $st->execute();
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        return false === $row ? [] : $row;
    }

    /**
     * @param string $sql the sql statement being excuted
     * @param array $params params being bind to the sql statement
     * @return array array of columns retrieved from database
     */
    public static function all(string $sql, array $params = [])
    {
        if (!empty($params)) {
            // configureed to trow Exception on fail
            $st = static::getDb()->prepare($sql);
            $binded = static::bindParam($st, $sql, $params);
            if (false === $binded) {
                return [];
            }
        } else {
            $st = static::getDb()->query($sql);
        }
        $st->execute();
        $row = $st->fetchAll(\PDO::FETCH_ASSOC);
        return false === $row ? [] : $row;
    }

    public function execute($sql, $params = [])
    {
        // configureed to trow Exception on fail
        $st = static::getDb()->prepare($sql);
        static::bindParam($st, $sql, $params);
        return $st->execute();
    }

    /**
     * @param \PDOStatement $pdoStatement
     * @param string $sql a prepared sql statement
     * @param array $params parameters need to bind to the sql statement
     */
    public static function bindParam(\PDOStatement $pdoStatement, $sql, $params)
    {
        foreach ($params as $n => $v) {
            if (false === strpos($sql, $n)) {
                unset($params[$n]);
            }
        }
        foreach ($params as $n => $v) {
            $binded = $pdoStatement->bindValue($n, $v, self::PDOType($v));
            if (false === $binded) {
                return false;
            }
        }
        return true;
    }

    /**
     * 想想怎么描述这个功能和可能存在的结果集覆盖问题
     * @param string $sql
     * @param array $params
     * @return array
     * @throws \Exception if the query result contains less than 2 colunms
     */
    public static function map(string $sql, array $params = [])
    {
        $rows = static::all($sql, $params);
        $row = reset($rows);
        if (false === $row) return [];
        if (count($row) !== 2) {
            throw new \Exception('the count of select items must be 2,but ' . count($row) . ' given');
        }
        $ret = [];
        foreach ($rows as $i => $row) {
            $first = reset($row);
            // @todo need to test
            $second = next($row);
            $ret[$first] = $second;
        }
        return $ret;
    }

    /**
     * @access
     * @param array $rows
     * @param string $index
     * @return array
     */
    public static function indexBy(array $rows, string $index)
    {
        $ret = [];
        foreach ($rows as $i => $row) {
            if (!array_key_exists($index, $row))
                throw new \Exception('every row of rows should has key ' . $index);
            $ret[$row[$index]] = $row;
        }
        return $rows;
    }

    /**
     * @access
     * @param array $ret1
     * @param array $ret2
     * @param string $on
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     *
     *
     * Description:
     *
     * request params:
     * name |meaning
     * ---|---
     *
     *
     *
     */
    public static function interSection(array $ret1, array $ret2, string $on)
    {
        $ret = [];
        $ret1 = self::indexBy($ret1, $on);
        $ret2 = self::indexBy($ret2, $on);
        foreach ($ret1 as $on => $row) {
            if (array_key_exists($on, $ret2)) {
//     @todo 重点测试这个方法,了解其工作方式
                $ret[$on] = $ret1[$on] + $ret2[$on];
            }
        }
    }

    /**
     * @access
     * @param array $ret1
     * @param array $ret2
     * @param string $on
     * @return array
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     *
     *
     * Description:
     *
     * request params:
     * name |meaning
     * ---|---
     *
     *
     *
     */
    public static function leftJoin(array $ret1, array $ret2, string $on)
    {
        $ret = [];
        $ret1 = self::indexBy($ret1, $on);
        $ret2 = self::indexBy($ret2, $on);
        if (empty($ret2)) return $ret1;
        $counms2 = array_keys(reset($ret2));
        foreach ($ret1 as $on => $row) {
            if (!array_key_exists($on, $ret2)) {
                foreach ($counms2 as $key) {
                    $counms2[$key] = "";
                }
            }
            $ret[$on] = $ret1[$on] + $ret2[$on];
        }
        return $ret;
    }


    public static function where()
    {

    }

    /**
     * @param $value
     * @return string
     * Description:
     * 类型转换,安全存储
     * bugfix: swich 部分未用 gettype
     */
    public static function PDOType($value)
    {
        switch (gettype($value)) {
            case 'string':
                return \PDO::PARAM_STR;
            case 'boolean':
                return \PDO::PARAM_BOOL;
            case 'integer':
            case 'double':
                return \PDO::PARAM_INT;
            case 'NULL':
                return \PDO::PARAM_NULL;
            default:
                throw new \Exception('column value could not be reference type!');
        }
    }

    /**
     * Returns a value indicating whether the give value is "empty".
     *
     * The value is considered "empty", if one of the following conditions is satisfied:
     *
     * - it is `null`,
     * - an empty string (`''`),
     * - a string containing only whitespace characters,
     * - or an empty array.
     *
     * @param mixed $value
     * @return boolean if the value is empty
     */
    protected function isEmpty($value)
    {
        return $value === '' || $value === [] || $value === null || is_string($value) && trim($value) === '';
    }

}