<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2/17/17
 * Time: 9:28 PM
 */

namespace nxn\db;


use Log;

/**
 * Description: description
 */
class Query
{

    /**
     * @return \PDO
     */
    public static function getMasterDb()
    {
        return \N::$app->getMasterDb();
    }

    /**
     * @return \PDO
     */
    public static function getSlaveDb()
    {
        return \N::$app->getSlaveDb();
    }

    public static function bindParams($sql, $params)
    {
        // static 变量的初始化语句只运行一次
        static $counter = 0;
        $counter++;

        if ($params) {
            // quote params first
            foreach ($params as $name => $param) {
                $params[$name] = self::safeString($param);
            }
            $sql = strtr($sql, $params);
        }
        Log::sql('[' . $sql . ']');
        return $sql;
    }

    /**
     * @param $sql
     * @param array $params
     * @return array return one dimensional array represent a row in table or empty array if can not find
     */
    public static function one(string $sql, array $params = [])
    {
        $sql = self::bindParams($sql, $params);

        $st = static::getSlaveDb()->query($sql);
        if (false === $st) return [];
        return $row = $st->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Fetch the first column from the first row in the result set
     * @param string $sql
     * @param array $params
     * @return bool|mixed
     */
    public static function scalar(string $sql, array $params = [])
    {
        $sql = self::bindParams($sql, $params);
        $st = static::getSlaveDb()->query($sql);
        if (false === $st) return false;
        $row = $st->fetch(\PDO::FETCH_COLUMN);
        \Log::info(__METHOD__ . ':' . json_encode($row, JSON_UNESCAPED_UNICODE));
        return $row;
    }


    /**
     * @param string $sql the sql statement being excuted
     * @param array $params params being bind to the sql statement
     * @return array array of columns retrieved from database
     */
    public static function all(string $sql, array $params = [])
    {
        $sql = self::bindParams($sql, $params);
        $st = static::getSlaveDb()->query($sql);
        if (false === $st) return [];
        return $st->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function execute($sql, $params = [])
    {
        $sql = self::bindParams($sql, $params);
        return static::getMasterDb()->exec($sql);
    }


    public static function keyPair(string $sql, array $params = [])
    {
        $sql = self::bindParams($sql, $params);
        $st = static::getSlaveDb()->query($sql);
        if (false === $st) return [];
        return $st->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_KEY_PAIR);
    }

    /**
     * @deprecated
     * for example: if the sql is: `select id ,name from user`,the the reult will be
     * [ 'id1' => 'name1', 'id2' => 'name2]]
     * @param string $sql
     * @param array $params
     * @return array
     *
     * @throws \Exception if the query result contains less than 2 colunms
     */
    public static function map(string $sql, array $params = [])
    {
        $rows = static::all($sql, $params);
        $row = reset($rows);
        if (false === $row) return [];
        if (count($row) !== 2) {
            throw new \Exception('the must be 2 columns in selection part of the sql statement, 
            but ' . count($row) . ' selected');
        }
        $ret = [];
        foreach ($rows as $i => $row) {
            $first = reset($row);
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
            // 如果结果2中对应有相同的行，就合并
            if (array_key_exists($on, $ret2)) {
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
                // 将第二列的内容设置为空，想一想没有有必要这么做？
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

    /**
     * @param $value
     * @return string
     * Description:
     */
    public static function safeString($value)
    {
        switch (gettype($value)) {
            // such as id in (1, 2, 4) 用于支持子查询
            case 'array':
                foreach ($value as $k => $v) {
                    $value[$k] = self::safeString($v);
                }
                return join(',', $value);
            case 'string':
                return self::getSlaveDb()->quote($value);
            case 'boolean':
                return ($value ? "TRUE" : 'FALSE');
            case 'integer':
            case 'double':
                return (string)$value;
            case 'NULL':
                return 'NULL';
            case 'object':
                if (method_exists($value, 'toArray')) {
                    $value = $value->toArray();
                    foreach ($value as $k => $v) {
                        $value[$k] = self::safeString($v);
                    }
                    return join(',', $value);
                } elseif (method_exists($value, '__tostirng')) {
                    return self::getSlaveDb()->quote($value);
                } else {
                    \Log::columnValueCouldNotBeNonArrayableOrNonStringifyObject($value);
                    throw new \Exception('column value could not be reference type!', 500);
                }
            default:
                \Log::columnValueCouldNotBeResource($value);
                throw new \Exception('column value could not be reference type!', 500);
        }
    }
}
