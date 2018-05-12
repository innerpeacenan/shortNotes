<?php
namespace nxn\db;

use PDO;

class ActiveRecord
{
    /**
     * @var PDO
     */
    public $db;

    /**
     * @var  string scenario for validate and map front end input to column of model
     */
    public $scenario = 'default';

    /**
     * @var array validate rule
     * 只申明部分不许要应用的场景,未申明则整体应用
     * [
     *  [column,'validate handler',['except scenario list']]
     * ]
     * 在这种情况下,注册的 handler会顺次将 column1 和 column2 的值作参数传输
     * [
     *  [
     *      [column1,column2],
     *      'validate handler',
     *      ['except scenario list']
     *  ]
     * ]
     */
    public static $rules = [];

    /**
     * @var array
     * [
     *    'default'=>['fontEndName'=>'backEndName'];
     *  ]
     * try to make front end and backend work separately
     */
    protected static $map;

    /**
     * @var array
     * bug fix: 之前给 primaryKey 默认值为 ['id'] ,在 getTableSchema 方法中又对此进行了叠加,
     */
    protected $_primaryKey = [];


    /**
     * @var string
     */
    protected $_autoIncrement = 'id';
    /**
     * @var array
     * mysql timestamp 数据类型只能通过字符串比较 日期(2017-05-08) 和 日期时间(2017-05-04 19:02:38) 格式都支持
     * null  true 表示允许为空
     * ['columnName'=>['Type'=>'boolearn|integer|double|string','Null'=>true,'Default'=>'CURRENT_TIMESTAMP',Comment=>'comment']
     */
    protected $_columns = [];

    /**
     * @var array
     */
    protected $_attributes = [];

    /**
     * @var array
     */
    protected $_oldAttributes = [];

    /**
     * @var array
     */
    protected $_dirtyAttributes = [];
    /**
     * @var array
     */
    protected $_errors;

    /**
     * @var object
     */
    protected $_validator;
    /**
     * @var array
     *  mysql type vs php type
     */
    protected static $_typeCast = [
        'int' => 'integer',
        'tinyint' => 'integer',
        'char' => 'string',
        'varchar' => 'string',
        'text' => 'string',
        'timestamp' => 'string',
        'enum' => 'string',
        'decimal' => 'double',
        'float' => 'float',
    ];

    public function __construct()
    {   // 确定db
        $this->db = \N::$app->getMasterDb();// 模型
        $this->tableSchema();
    }

    /**
     * @access
     * @return string
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     * Description:
     * 子类必须重载该方法
     */
    public static function tableName()
    {
        throw new \Exception('tableName must be override by subClass of ' . get_class());
    }

    public function getValidator()
    {
        if ($this->_validator === null) {
            $this->setValidator();
        }
        return $this->_validator;
    }

    public function setValidator(object $validator = null)
    {
        if (!isset($validator)) {
            // @todo 重新想想依赖加载怎么写合适
            $this->_validator = \N::createObject(\N::$app->conf['validator']);
        } else {
            $this->_validator = $validator;
        }
    }

    public function getAttribute()
    {
        return $this->_attributes;
    }

    /**
     * @param array $columns
     * @return void
     * Description:
     * it is implecit called when use [$this->attributes = values]
     */
    public function setAttributes(array $columns)
    {
        foreach ($columns as $name => $value) {
            // 如主键已近存在，则直接过滤
            if (in_array($name, $this->_primaryKey) and array_key_exists($name, $this->_attributes)) {
                continue;
            }
            $this->$name = $value;
        }
    }

    public function getOldAttribute()
    {
        return $this->_oldAttributes;
    }

    /**
     * @param $tableName string
     * @return boolean
     * @throws \Exception
     * @todo 该方法负责解读所有的表模式
     * Description:
     * array (
     * 0 =>
     * array (
     * 'field' => 'id',
     * 'Type' => 'int(10) unsigned', | tinyint(3) unsigned | timestamp | ['draft','enable']
     * 'Collation' => NULL,
     * 'Null' => 'NO',
     * 'Key' => 'PRI',
     * 'Default' => NULL,
     * 'Extra' => 'auto_increment',
     * 'Privileges' => 'select,insert,update,references',
     * 'Comment' => '主键',
     * ),
     */
    public function tableSchema($tableName = null)
    {
        $tableName = $tableName ?? $this->tableName();
        $st = $this->db->query('show full columns from ' . static::quoteName($tableName));
        if (false === $st) return false;
        $columns = $st->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            $name = $column['Field'];
            $this->_columns[$name]['Null'] = $column['Null'] === 'NO' ? false : true;
            $this->_columns[$name]['Default'] = $column['Default'];
            $this->_columns[$name]['Comment'] = $column['Comment'];
            if ('PRI' === $column['Key']) $this->_primaryKey[] = $name;
            if ('auto_increment' === $column['Extra']) $this->_autoIncrement = $name;
            // int(10) unsigned
            $result = preg_match('/^\w+/', $column['Type'], $match);
            if ($result) {
                $sqlColumnType = $match[0];
                if (isset(static::$_typeCast[$sqlColumnType])) {
                    $this->_columns[$name]['Type'] = static::$_typeCast[$sqlColumnType];
                } else {
                    throw new \Exception('mysql type:' . $sqlColumnType . ' is not included in type map yet!');
                }
            } else {
                throw new \Exception('正则匹配错误,type is:' . $columns['Type']);
            }
        }
        return true;
    }


    /**
     * @param integer| array $primary 如果是数组的话,表示是联合主键键值对
     * @return static | null
     *
     * Description:
     */
    public static function load($primary)
    {
        /** @var static $ar */
        $ar = \N::createObject(['class' => get_called_class()]);
        $tableName = self::quoteName($ar->tableName());
        // 联合主键
        $conditon = [];
        if (is_string($primary) || is_integer($primary)) {
            $columnName = $ar->_primaryKey[0];
            $primary = [$columnName => $primary];
        }
        if (!is_array($primary)) {
            if (getenv(N_DEBUG)) {
                throw new \Exception("the first parameter pass to " . __METHOD__ .
                    ' need to be string, integer or array in hash format');
            }
            return null;
        };
        foreach ($ar->_primaryKey as $column) {
            $valType = $ar->_columns[$column]['Type'];
            $val = $primary[$column];
            $validType = settype($var, $valType);
            if (false === $validType) {
                throw new \Exception('invalid varible ' . print_r($var) . ' set be set for column:' . $column);
            }
            $conditon[] = self::quoteName($column) . '=' . $ar->safeString($val);
        }
        /**
         * @var PDO $db
         */
        $db = $ar->db;
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE ' . join(' AND ', $conditon);
        \Log::sql('[' . $sql . ']');
        $st = $db->query($sql, PDO::FETCH_ASSOC);
        if (!$st) return null;
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        foreach ($row as $name => $value) {
            settype($value, $ar->_columns[$name]['Type']);
            $ar->_oldAttributes[$name] = $ar->_attributes[$name] = $value;
        }
        return $ar;
    }

    /**
     * @access
     * @return bool
     * Description:
     * 根据脏值,对每条结果进行验证
     * 确保根据第二个参数可以找到对应的回调函数
     * [column1,column2],'validate handler',['except scenario list']
     */
    public function validate()
    {
        if (empty(static::$rules)) return true;
        foreach (static::$rules as $rule) {
            $colunms = $rule[0];
            if (is_string($colunms)) {
                $colunms = [$colunms];
            }
            if (!is_array($colunms)) {
                throw new \Exception('invalidate rule:' . var_export($rule));
            }
            $data = [];
            foreach ($colunms as $name) {
                if (!isset($this->_dirtyAttributes[$name]))
                    break;
                $data[] = $this->_dirtyAttributes[$name];
            }
            // 检查所执行的回调函数
            if ($data && isset($rule[1])) {
                $fun = $rule[1];
                if (is_string($fun)) {
                    if (is_callable([$this->getValidator(), $fun])) {
                        $fun = [$this->getValidator(), $fun];
                    } else if (is_callable([$this, $fun])) {
                        $fun = [$this, $fun];
                    } else {
                        throw new \Exception('invalid call back function name:' . var_export($fun));
                    }
                } else if (!is_callable($fun)) {
                    throw new \Exception('invalid call back function name:' . var_export($fun));
                }
                $valid = call_user_func_array($rule[1], $data);
                if (!$valid) return false;
            }
        }
        return true;
    }

    /**
     * @access
     * @return boolean | integer
     * Description:
     * return 1 if inserted, false on validate failure
     */
    public function insert($validate, $transaction = true)
    {
        if (true === $validate) {
            $valid = $this->validate();
            if (!$valid) return false;
        }
        $tableName = self::quoteName($this->tableName());
// 存放字段值与字段名称的映射关系
        $kv = [];
//按照 columns插入,保证除了自增主键外,其他各个键都有值插入
        if (empty($this->_attributes)) return 0;
        $this->beforeInsert();
        foreach ($this->_columns as $name => $def) {
            //如果值为null,检查是否允许未null,如果允许,跳过.不允许,检查是否有默认值,如果有默认值,则赋予默认值,否则,直接返回
            if (in_array($name, $this->_primaryKey) || true === $this->_columns[$name]['Null']) {
                continue;
            }
            if (isset($this->_attributes[$name])) {
                $kv[self::quoteName($name)] = $this->safeString($this->_attributes[$name]);
            } else if (false === $this->_columns[$name]['Null'] && isset($this->_columns[$name]['Default'])) {
                $val = $this->columnDefaultValue($def['Default']);
                settype($val, $def['Type']);
                $this->_attributes[$name] = $val;
                $kv[self::quoteName($name)] = $this->safeString($val);
            } else {
                if (getenv(N_DEBUG)) {
                    throw new \Exception($tableName . '.' . $name . ' must have value when insert!');
                }
                return 0;
            }
        }
        $colums = join(',', array_keys($kv));
        $values = join(',', array_values($kv));
        $sql = 'INSERT INTO ' . $tableName . ' ( ' . $colums . ' ) VALUES ( ' . $values . ')';
        if (false === $transaction) {
            return $this->doInsert($sql);
        }
        if (false === $this->db->inTransaction()) {
            $this->db->beginTransaction();
        }
        try {
            $status = $this->doInsert($sql);
            $this->db->commit();
            return $status;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    protected function beforeInsert()
    {

    }

    protected function doInsert($sql)
    {
        \Log::sql('[' . $sql . ']');
        $status = $this->db->exec($sql);
        if ($status) {
            $this->_attributes[$this->_autoIncrement] = (int)$this->db->lastInsertId();
            $this->_oldAttributes = $this->_attributes;
            $this->afterInsert();
        }
        return $status;
    }

    /**
     * @access
     * @param bool $validate
     * @return bool|int
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     *
     *
     * Description:
     * 要求主键不能从0开始,前端有时候会传 0 过来
     *
     *
     */
    public function save(bool $validate = false)
    {
        if ($validate) {
            $valid = $this->validate();
            if (!$valid) return false;
        }
        if (empty($this->_attributes)) return 0;
        if (empty($this->_attributes[reset($this->_primaryKey)])) {
            $status = $this->insert(false);
        } else {
            $status = $this->update(false);
        }
        return $status;
    }

    public function afterInsert()
    {
        $this->_dirtyAttributes = [];
    }

    /**
     * @access
     * @return  int | boolean return false if input is not valid
     * Description:
     * bugfix: 检查脏值是否为空数组,如果没有数据变化,直接返回0
     */
    public function update($validate = true, $transaction = true)
    {
        if ($validate) {
            $valid = $this->validate();
            if (!$valid) return false;
        }
        $tableName = self::quoteName($this->tableName());
        $this->getDirtyAttributes();
        $set = [];
        foreach ($this->getDirtyAttributes() as $name => $value) {
            $set[] = self::quoteName($name) . ' = ' . $this->safeString($value);
        }
        if (empty($set)) return 0;
        $condition = [];
        foreach ($this->_primaryKey as $column) {
            $val = $this->_attributes[$column];
            $condition[] = self::quoteName($column) . ' = ' . $this->safeString($val);
        }
//        update set 部分是用逗号连接的
        $sql = 'UPDATE ' . $tableName . ' SET ' . join(',', $set) . ' WHERE ' . join(' AND ', $condition);
        if (false === $transaction) return $this->doUpdate($sql);
        if (false === $this->db->inTransaction()) {
            $this->db->beginTransaction();
        }
        try {
            $affectedNum = $this->doUpdate($sql);
            $this->db->commit();
            return $affectedNum;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }


    protected function doUpdate($sql)
    {
        \Log::sql('[' . $sql . ']');
        $affectedNum = $this->db->exec($sql);
        if ($affectedNum) {
            $this->afterUpdate();
        }
        return $affectedNum;
    }


    public function afterUpdate()
    {
        $this->_dirtyAttributes = [];
    }

    /**
     * @return int
     * Description:
     *
     */
    public function delete($transaction = true)
    {
        $tableName = static::quoteName($this->tableName());
        $condition = [];
        foreach ($this->_primaryKey as $column) {
            if (!isset($this->_attributes[$column])) return false;
            $condition[] = self::quoteName($column) . ' = ' . $this->safeString($this->_attributes[$column]);
        }
        $sql = 'DELETE FROM ' . $tableName . ' WHERE ' . join(' AND ', $condition);
        if (false === $transaction) return $this->doDelete($sql);
        if (false === $this->db->inTransaction()) {
            $this->db->beginTransaction();
        }
        try {
            $status = $this->doDelete($sql);
            $this->db->commit();
            return $status;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @access
     * @return int
     */
    protected function doDelete($sql)
    {
        \Log::sql('[' . $sql . ']');
        $affectedNum = $this->db->exec($sql);
        if ($affectedNum) $this->afterDelete();
        return $affectedNum;
    }


    public function afterDelete()
    {
        $this->_dirtyAttributes = [];
    }

    /**
     * @access
     * @return array|null
     *
     * Description:
     */
    public function getDirtyAttributes()
    {
        if (empty($this->_dirtyAttributes)) {
            foreach ($this->_attributes as $column => $value) {
// Undefined index: name, because oldAttributes might be empty
                if (isset($this->_oldAttributes[$column]) && ($value === $this->_oldAttributes[$column])) {
                    continue;
                } else {
                    $this->_dirtyAttributes[$column] = $value;
                }
            }
        }
        return $this->_dirtyAttributes;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     * @throws \Exception if column does not exists in table
     * Description:
     * 已经考虑了 映射了没,
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            // 之前没有将 value 传递过去看
            call_user_func([$this, $setter], $value);
            return;
        }
        if (is_array($this->_attributes) && array_key_exists($name, $this->_columns)) {
            settype($value, $this->_columns[$name]['Type']);
            $this->_attributes[$name] = $value;
        } else if (isset(static::$map[$this->scenario][$name])) {// 如果映射关系存在的话
            $name = static::$map[$this->scenario][$name];
            settype($value, $this->_columns[$name]['Type']);
            $this->_attributes[$name] = $value;
        } else {
            //@todo  可在 debug 模式下记录更多日志内容(包括请求的路由,方便表结构变更后,开展进行代码清理工作)
            throw new \Exception('column :' . $name . ' does not exists in table ' . $this->tableName());
        }
    }

    /**
     * @access
     * @param $name
     * @return string | integer
     *
     * Description:
     *
     *
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return call_user_func([$this, $getter], $name);
        }
        if (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        } elseif (isset(static::$map[$this->scenario][$name])) {
            $name = static::$map[$this->scenario][$name];
            return $this->_attributes[$name];
        } else {
            throw new \Exception('column :' . $name . ' does not exists in table ' . $this->tableName());
        }
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
     * Description:
     */
    public function safeString($value)
    {
        switch (gettype($value)) {
            case 'string':
                return $this->db->quote($value);
            case 'boolean':
                return ($value ? "TRUE" : 'FALSE');
            case 'integer':
            case 'double':
                return (string)$value;
            case 'NULL':
                return 'NULL';
            default:
                throw new \Exception('column value could not be reference type!');
        }
    }

    /**
     * @access
     * @param $default
     * @return false|null|string
     *
     * Description:
     * @todo 待处理其他类型的默认值
     */
    public static function columnDefaultValue($default)
    {
        if (0 === strcasecmp($default, 'CURRENT_TIMESTAMP')) {
            return date('Y-m-d H:i:s');
        }
        if (0 === strcasecmp($default, 'NUll')) {
            return null;
        }
//   is_numeric — Finds whether a variable is a number or a numeric string
        return $default;
    }
}
