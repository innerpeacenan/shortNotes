<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2/17/17
 * Time: 9:28 PM
 */
namespace nxn\db;

/**
 * Description: description
 * @todo 这个类待进一步测试
 */
class Query
{

    /**
     * @return \PDO
     * @todo 后期可以修改，以支持读写分离
     */
    public static function getMasterDb()
    {
        return \N::$app->get('db');
    }

    /**
     * @return \PDO
     */
     public static function getSlaveDb()
    {
        return \N::$app->get('db');
    }

    /**
     * @param $sql
     * @param array $params
     * @return array return one dimensional array represent a row in table or empty array if can not find
     */
    public static function one(string $sql, array $params = [])
    {

        if($params) $sql = strtr($sql, $params);
        $st = static::getSlaveDb()->query($sql);
        if(false === $st) return  [];
        return $row = $st->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql the sql statement being excuted
     * @param array $params params being bind to the sql statement
     * @return array array of columns retrieved from database
     */
    public static function all(string $sql, array $params = [])
    {
        if($params) $sql = strtr($sql, $params);
        $st = static::getSlaveDb()->query($sql);
        if(false === $st) return [];
        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function execute($sql, $params = [])
    {
        if($params) $sql = strtr($sql, $params);
        return static::getMasterDb()->exec($sql);
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
     */
    public static function interSection(array $ret1, array $ret2, string $on)
    {
        $ret = [];
        $ret1 = self::indexBy($ret1, $on);
        $ret2 = self::indexBy($ret2, $on);
        foreach ($ret1 as $on => $row) {
            if (array_key_exists($on, $ret2)) {
//@todo 重点测试这个方法,了解其工作方式
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