<?php
namespace nxn\db;

use nxn\web\Application;
use PDO;
use yii\base\Exception;

class ActiveRecord
{
    /**
     * @var PDO
     */
    public static $db;

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
    protected static $_primaryKey = [];


    /**
     * @var string
     */
    protected static $_autoIncrement = 'id';
    /**
     * @var array
     * mysql timestamp 数据类型只能通过字符串比较 日期(2017-05-08) 和 日期时间(2017-05-04 19:02:38) 格式都支持
     * 如果 [[type]] 是数组,则表示枚举类型,该数据用于检查输入的合法性
     * //@todo 注释需要进一步补全
     * ['columnName'=>['type'=>'boolearn|integer|double|string','allowNull'=>true,'default'=>'CURRENT_TIMESTAMP',comment=>'comment']
     */
    protected static $_columns = [];

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
     */
    protected static $_typeCast = [
        'int' => 'integer',
        'tinyint' => 'integer',
        'char' => 'string',
        'varchar' => 'string',
        'text' => 'string',
        'timestamp' => 'string',
        'enum' => 'string',
        'decimal' => 'double'
    ];

    public function __construct()
    {   // 确定db
        if (!isset(static::$db)) {
            static::$db = \N::$app->db;
//            static::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
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
        return '';
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

    /**
     * @param array $columns
     * @return void
     * Description:
     * it is implecit called when use [$this->attributes = values]
     */
    public function setAttributes(array $columns)
    {
        // 先检查 scenario,确定 map, 然后赋值
        // 还要检查 column存不存在,不存在直接过滤掉
        foreach ($columns as $name => $value) {
//      这样可以保证 AR 赋予了正确的类型
            $this->$name = $value;
        }
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
        if (!empty(static::$_columns)) {
            return true;
        }
        if (empty($tableName)) {
            $tableName = $this->tableName();
        }
        // PDOStatement <b>PDO::query</b> returns a PDOStatement object, or <b>FALSE</b>on failure.
        $st = static::$db->query('show full columns from ' . static::quoteName($tableName));
        if (false === $st) return false;
        $columns = $st->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            $name = $column['Field'];
//          得到字段是否为空
//            bug fix history: 之前这一块逻辑写反了 mysql Null===='NO' 表示不允许为空
            static::$_columns[$name]['allowNull'] = $column['Null'] === 'NO' ? false : true;
            static::$_columns[$name]['default'] = $column['Default'];
            static::$_columns[$name]['comment'] = $column['Comment'];
// 养成将常量放到比较代码的前面的好习惯,这样如果写错了会抛出一个语法分析错误
            if ('PRI' === $column['Key']) {
                static::$_primaryKey[] = $name;
            }
            if ('auto_increment' === $column['Extra']) {
                static::$_autoIncrement = $name;
            }
            // deal with type
            if (preg_match('/^(\w+)(?:\(([^\)]+)\))?/', $column['Type'], $match)) {
                // 检查目前是否支持该类型的数据转化
                if (isset(static::$_typeCast[$match[1]])) {
                    static::$_columns[$name]['type'] = static::$_typeCast[$match[1]];
                } else {
                    throw new \Exception('mysql type:' . $name . ' is not included in type map yet!');
                }
            }
        }
        return true;
    }


    /**
     * @access
     * @param integer| array $primaryKeyVal 如果是数组的话,表示是联合主键键值对
     * @return $this | null
     *
     * Description:
     * bug: 之前传入不是数字,当然不是字符串了
     * bug: 之前传入的字符串未进行类型转换,查询的主键可能是直接从前台传递过来的
     */
    public function load($primaryKeyVal)
    {
        $tableName = self::quoteName($this->tableName());
        // 联合主键
        $conditon = [];
        $params = [];
        if (is_array($primaryKeyVal)) {
            foreach (static::$_primaryKey as $column) {
                //  bug fixed: 之前用错了 Undefined index: id
                $conditon[] = self::quoteName($column) . '=:' . $column;
                $valType = static::$_columns[$column]['type'];
                $val = $primaryKeyVal[$column];
                $validType = settype($var, $valType);
                if ($valType === false) {
                    throw new Exception('invalid varible set to column:' . $column . var_dump($val));
                }
                settype($val, static::$_columns[$column]['type']);
                $params[':' . $column] = [$val, $this->PDOType($val)];
            }
        } elseif (is_string($primaryKeyVal) || is_integer($primaryKeyVal)) {
            $column = static::$_primaryKey[0];
            $conditon[] = static::quoteName($column) . ' =:' . $column;
//            进行必要的类型转化, 查询的值可能是直接从前端传过来的
            settype($primaryKeyVal, static::$_columns[$column]['type']);
            $params[':' . $column] = [$primaryKeyVal, $this->PDOType($primaryKeyVal)];
        } else {
            return null;
        }
        $st = static::$db->prepare('SELECT * FROM ' . $tableName . ' WHERE ' . join(' AND ', $conditon));
        foreach ($params as $param => $val) {
            $st->bindValue($param, $val[0], $val[1]);
        }
        $st->execute();
        if (!$st) return null;
        $row = $st->fetch(PDO::FETCH_ASSOC);
//      Invalid argument supplied for foreach(),because it may return false
        if (!$row) return null;
        foreach ($row as $name => $value) {
            // 对加载的值进行了类型转换
            // bool true on success or false on failure. 不能将settype()的结果直接赋值给变量
            settype($value, static::$_columns[$name]['type']);
            $this->_oldAttributes[$name] = $this->_attributes[$name] = $value;
        }
        return $this;
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
     * 在考虑有没有必要写一套查询构建器
     *
     */
    public function insert($validate)
    {
        if ($validate) {
            $valid = $this->validate();
            if (!$valid) return false;
        }
        $tableName = self::quoteName($this->tableName());
        $set = [];
        $params = [];
//        按照 attributes 去插入,保证除了自增主键外,其他各个键都有值插入
        if (empty($this->_attributes)) return 0;
        foreach (static::$_columns as $name => $def) {
            //如果值未null,检查是否允许未null,如果允许,跳过.不允许,检查是否有默认值,如果有默认值,则赋予默认值,否则,直接返回
            if (in_array($name, static::$_primaryKey) || true === static::$_columns[$name]['allowNull']) {
//@ 完善默认值处理方案,保证插入后,每个元素都是有值的
                continue;
            }
            if (isset($this->_attributes[$name])) {
                $set[self::quoteName($name)] = ':' . $name;
                $val = $this->_attributes[$name];
                $params[':' . $name] = [$val, $this->PDOType($val)];
            } else if (static::$_columns[$name]['allowNull'] === false && isset(static::$_columns[$name]['default'])) {
//           要传递字符串,而不是数组
                $val = $this->columnDefaultValue($def['default']);
                settype($val, $def['type']);
                $this->_attributes[$name] = $val;
                $set[self::quoteName($name)] = ':' . $name;
                $params[':' . $name] = [$val, $this->PDOType($val)];
            } else {
//             todo add information to error message
                return 0;
            }
        }
        $colums = join(',', array_keys($set));
        $values = join(',', array_values($set));
        $insert = 'INSERT INTO ' . $tableName . ' ( ' . $colums . ' ) VALUES ( ' . $values . ')';
        $st = static::$db->prepare($insert);
        foreach ($params as $param => $val) {
            $st->bindValue($param, $val[0], $val[1]);
        }
        //@todo use try catch if you what to handle with transatcion
        $status = $st->execute();
        if (false !== $status) {
            // 设置自增键
            $this->_attributes[static::$_autoIncrement] = static::$db->lastInsertId();
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
    public function save(bool $validate)
    {
        if ($validate) {
            $valid = $this->validate();
            if (!$valid) return false;
        }
        $tableName = self::quoteName($this->tableName());
        $set = [];
        $params = [];
//        按照 attributes 去插入,保证除了自增主键外,其他各个键都有值插入
        if (empty($this->_attributes)) return 0;
        // 插入和更新的程序写反了
        if (empty($this->_attributes[static::$_primaryKey[0]])) {
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
     * 'name' => ''unit test'' 为什么加了一对引号呢? 最主要还没有转义 弄不明白
     * 绑定的语句在 update 部分成功,将这部分稍作优化和进一步测试后,移植到其他方法上面
     */
    public function update($validate = true)
    {
        if ($validate) {
            $valid = $this->validate();
            if (!$valid) return false;
        }
        $tableName = self::quoteName($this->tableName());
        $this->getDirtyAttributes();
        $params = [];
        $set = [];
        foreach ($this->getDirtyAttributes() as $name => $value) {
            $set[] = self::quoteName($name) . ' = :set_' . $name;
            $params[':set_' . $name] = [$value, $this->PDOType($value)];
        }
        if (empty($set)) return 0;
        $condition = [];
        foreach (static::$_primaryKey as $column) {
            $condition[] = self::quoteName($column) . '=:update_' . $column;
            $val = $this->_attributes[$column];
            $params[':update_' . $column] = [$val, $this->PDOType($val)];
        }
//        update set 部分是用逗号连接的
        $update = 'UPDATE ' . $tableName . ' SET ' . join(',', $set) . ' WHERE ' . join(' AND ', $condition);
        $st = static::$db->prepare($update);
        foreach ($params as $param => $value) {
            $st->bindValue($param, $value[0], $value[1]);
        }
        $affectedNum = $st->execute();
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
    public function delete()
    {
        $tableName = static::quoteName($this->tableName());
        $condition = [];
        $params = [];
        foreach (static::$_primaryKey as $column) {
            if (!isset($this->_attributes[$column])) return false;
            $condition[] = self::quoteName($column) . ' = :' . $column;
            $params[':' . $column] = [$this->_attributes[$column], $this->PDOType($this->_attributes[$column])];
        }
        $st = static::$db->prepare('DELETE FROM ' . $tableName . ' WHERE ' . join(' AND ', $condition));
        foreach ($params as $param => $val) {
            $st->bindValue($param, $val[0], $val[1]);
        }
        $status = $st->execute();
        if ($status) {
            $this->afterDelete();
        }
        return $status;
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
        if (is_array($this->_attributes) && array_key_exists($name, static::$_columns)) {
            settype($value, static::$_columns[$name]['type']);
            $this->_attributes[$name] = $value;
        } else if (isset(static::$map[$this->scenario][$name])) {// 如果映射关系存在的话
            $name = static::$map[$this->scenario][$name];
            settype($value, static::$_columns[$name]['type']);
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
     * @access
     * @param $default
     * @return false|null|string
     *
     * Description:
     * @todo 待处理其他类型的默认值
     *
     *
     *
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
