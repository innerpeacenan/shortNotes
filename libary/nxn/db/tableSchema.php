<?php
namespace nxn\db;

trait TableSchema
{
    /**
     * @var \PDO
     */
    public static $db;

    /**
     * @access
     * @param $table
     * @param $column
     * @param $comment
     * @return string
     * Description:
     *
     *
     */
    public function addCommentOnColumn($table, $column, $comment)
    {
        $definition = $this->getColumnDefinition($table, $column);
        $definition = trim(preg_replace("/COMMENT '(.*?)'/i", '', $definition));
        list($table, $column) = array_map([get_called_class(), 'quoteName'], [$table . $column]);
        $str = "ALTER TABLE %s CHANGE %s %s COMMENT %s";
        return static::$db->exec(sprintf($str, $table, $column, $definition, self::cast($comment)));
    }

    /**
     * @param $table
     * @param $comment
     * @return int
     *
     */
    public function addCommentOnTable($table, $comment)
    {
        return static::$db->exec('ALTER TABLE ' . static::quoteName($table) . ' COMMENT ' . static::quoteName($comment));
    }

    /**
     * Gets column definition.
     *
     * @param string $table table name
     * @param string $column column name
     * @return null|string the column definition
     * @throws \Exception in case when table does not contain column
     */
    private static function getColumnDefinition($table, $column)
    {
        $quotedTable = static::quoteName($table);
        $db = static::$db;
        $st = $db->query('SHOW CREATE TABLE ' . $quotedTable);
        if (false === $st) {
            throw new \Exception('table' . $table . ' does not exists!');

        }
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if (isset($row['Create Table'])) {
            $sql = $row['Create Table'];
        } else {
            $row = array_values($row);
            $sql = $row[1];
        }
        if (preg_match_all('/^\s*`(.*?)`\s+(.*?),?$/m', $sql, $matches)) {
            foreach ($matches[1] as $i => $c) {
                if ($c === $column) {
                    return $matches[2][$i];
                }
            }
        }
        return null;
    }

    /**
     * Quotes a simple table name for use in a query.
     * A simple table name should contain the table name only without any schema prefix.
     * If the table name is already quoted, this method will do nothing.
     * @param string $name table name
     * @return string the properly quoted table name
     */
    public static function quoteName($name)
    {
        return strpos($name, "`") !== false ? $name : '`' . $name . '`';
    }

    /**
     * @param $value
     * @return string
     * @throws \Exception
     * Description:
     * 类型转换,安全存储
     */
    public static function cast($value)
    {
        switch ($value) {
            case 'string':
                $value = static::$db->quote($value);
                break;
            case 'boolean':
                $value = $value === true ? 'true' : 'false';
                break;
            case 'integer' :
            case 'double' :
                break;
            default:
                throw new \Exception('column value could not be reference type!');
        }
        return $value;
    }
}
