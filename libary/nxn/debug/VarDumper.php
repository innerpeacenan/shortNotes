<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/14/17
 * Time: 8:04 PM
 */
namespace nxn\debug;


/**
 * Class VarDumper
 * @package libary\nxn\debug
 * User: xiaoning nan
 * Date: 2017-06-{14}
 * Time: xx:xx
 * Description: description
 */
class VarDumper
{
    /**
     * @var array
     *  yii 的程序是如何保证在调用的时候,该数据类型已近是数组了,回头走单步研究下
     */
    public static $object = [];
    public static $output;
    public static $depth;

    /**
     * @access
     * @param  mixed $var
     * @param integer $level 数组和对象的层级,标量的层级为0
     * @return void
     */
    public static function dumpInternal($var, $level)
    {
        $indentSymbol = '&nbsp;&nbsp;&nbsp;&nbsp;';
        $endOfLine = '<br/>';
        if (php_sapi_name() === 'cli') {
            $indentSymbol = '    ';
            $endOfLine = PHP_EOL;
        }
        switch (gettype($var)) {
            case 'boolean':
                self::$output .= $var ? 'true' : 'false';
                break;
            case 'NULL':
                self::$output .= 'null';
                break;
            case 'resource':
                self::$output .= '{resource}';
                break;
            case 'integer':
            case 'double':
                self::$output .= "$var";
                break;
//            之前吧字符串的形式给漏掉了
            case 'string';
                self::$output .= "'" . addslashes($var) . "'";
                break;
            case 'unknown type':
                self::$output .= '{unknown type}';
                break;
            case 'array';
                if (self::$depth < $level) {
                    self::$output .= '[...]';
                }
                if (empty($var)) {
                    self::$output .= '[]';
                }
                self::$output .= '[' . $endOfLine;
                $spaces = str_repeat($indentSymbol, 2 * $level);
                // 新起的一行在上一行的基础上,在增加四个空格
                $keys = array_keys($var);
                foreach ($keys as $key) {
                    self::$output .= $spaces . '    ';
//                    这里需要注意,dumpInternal 是没有返回值的,不能直接按找返回值拼接
                    self::dumpInternal($key, 0);
                    self::$output .= ' => ';
                    self::dumpInternal($var[$key], $level + 1);
                    self::$output .= ',' . $endOfLine;
                }
                self::$output .= $spaces . ']';
                break;
            case 'object';
//  如果之前输出过该对象的信息,则可以以简略方式表示,这样有利于更好的展示结果信息
//  array_search() expects parameter 2 to be array, null given
                if (($id = array_search($var, self::$object, true)) !== false) {
                    // $id + 1 ,从 1 开始计数,不要让从0开始计数
                    self::$output .= get_class($var) . '#' . ($id + 1) . '(...)';
                } elseif (self::$depth < $level) {
                    self::$output .= '(...)';
                } else {
                    $id = array_push(self::$object, $var);
                    $spaces = str_repeat(' ', 4 * $level);
                    $className = get_class($var);
//  Array to string conversion,原来这下面这一行没有以分数结尾,所以程序把它看作一行,会把处理完的数组转化未字符串
//                    php 的系统变量名大小写不规范,确实不好记住
                    if ('__PHP_Incomplete_Class' === $className && method_exists($var, '__debugInfo')) {
                        // 打印下 debug 的信息
                        $keys = $var->__debugInfo();
                        if (!is_array($keys)) {
                            throw new \Exception('__Debug_info() must return array');
                        }
                    } else {
//         [Cannot access property started with '\0'](https://cweiske.de/tagebuch/php-property-started-nul.htm)
//         \0*\0 protected   \0className\0 private
//  注意对象向数组的强转与 get_object_var 的区别, get_object_vars 只转化 public 类型的成员,而 强制可以转化所有非静态类型的变量
                        $keys = (array)$var;
                    }
                    self::$output .= "$className#$id\n$spaces(";
                    foreach ($keys as $key => $value) {
                        $key = strtr(trim($key), '\0', ':');
                        self::$output .= "\n" . $spaces . "    [$key] => ";
                        self::dumpInternal($value, $level + 1);
                    }
                    self::$output .= "\n" . $spaces . ')';
                }
                break;
            default:
                assert(1 == 0);
        }
    }

    /**
     * after filter the above tag;
     *```
     * &lt;\?php<br />
     * ```
     * the result is :
     * <code><span style="color: #000000">
     * <span style="color: #0000BB"></span>
     * </span>
     * </code>
     */
    public static function dumpAsString($var, $depth = 10, $heghtlight = true)
    {
        self::$object = [];
        self::$output = "";
        self::$depth = $depth;
        self::dumpInternal($var, 0);
        if (true === $heghtlight) {
            $result = highlight_string("<?php\n" . self::$output, true);
//   preg_replace(): No ending delimiter '/' found
            self::$output = preg_replace('/&lt;\\?php<br \\/>/', '', $result, 1);
        }
        return self::$output;
    }

    public static function dump($var, $depth = 10, $hightlight = false)
    {
        echo self::dumpAsString($var, $depth, $hightlight);
    }


    /**
     * @access
     * @param $var
     * @param string $str
     * @return void | string
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * @todo 这个方法有待进一步完善
     *
     *
     * Description:
     *  打印一个数组的源代码,方便粘贴复制,这里只是粗略的实现,接下来尝试跟优雅的实现,用字符串拼接实现一次性输入
     *```
     * $a = array(1 => 'apple', 'b' => true, 'c' => array($this, 'y', 'z'));
     * $this->var_export($a);
     *
     * php -r "echo '\\\\';"
     * result:
     * \
     *
     *```
     */
    public static function var_export(&$var, $return = false)
    {
        // tokens
        $indent = '  ';
        $doubleArrow = ' => ';
        $lineEnd = ',' . PHP_EOL;
        $newLine = PHP_EOL;
        $strDelim = '\'';
        // use find and repace
        $find = [null, '\\', '\''];
        $replace = ['NULL', '\\\\', '\\\''];
        $out = '';
        // deal with value
        switch (gettype($var)) {
            // deal with all variable types, that is : [integer,double,string,boolean,array,object,null,resource]
            case 'array':
                $out = '[' . $newLine;
                foreach ($var as $k => $v) {
                    // deal with key
                    if (is_string($k)) {
                        // make string key safe
//                        $k = strtr($k, $safe);
                        for ($i = 0, $c = count($find); $i < $c; $i++) {
                            $k = str_replace($find[$i], $replace[$i], $k);
                        }
                        $k = $strDelim . $k . $strDelim;
                    }

                    if (is_array($v)) {
                        // 数组每一层,换一行
                        $export = self::var_export($v, true);
                        $v = $newLine . $export;
                    } else {
                        $v = self::var_export($v, true);
                    }
                    // Piece line together (输出数组中的一个键值对)
//                    中间过程一定要用 . 相互连接, 在在这个循环的过程中,中间的结果是要保留的
                    $out .= $k . $doubleArrow . $v . $lineEnd;
                }
                // 拼接数组输出结果
                $out .= ']';
                break;
            case 'string':
                /**
                 * If replace_pairs contains a key which is an empty string (""), FALSE will be returned.
                 * If the str is not a scalar then it is not typecasted into a string, instead a warning is raised and NULL is returned.
                 */
//                $var = strtr($var, $safe);
                // 因为外层规定了单引号,所以里层的所有单引号都要转义
                for ($i = 0, $c = count($find); $i < $c; $i++) {
                    $var = str_replace($find[$i], $replace[$i], $var);
                }
                $out = $strDelim . $var . $strDelim;
                break;
            // Number
            case 'integer':
//for historical reasons "double" is returned in case of a float, and not simply "float"
            case 'double' :
                $out = (string)$var;
                break;
            case 'boolean':
                $out = $var === true ? 'true' : 'false';
                break;
//            NULL 与 resource 放在一起处理 the type of null is in upercase
            case 'NULL':
            case 'resource':
                $out = 'NULL';
                break;
            case 'object':
                // Start the object export
                $out = 'class ' . get_class($var) . '{' . $newLine;
                // deal with each property
                foreach (get_object_vars($var) as $p => $ov) {
                    $out .= ' var $' . $p . ' = ';
                    if (is_array($ov)) {
                        $export = self::var_export($ov, true);
                        $out .= $newLine . $export . ';' . $newLine;
                    } else {
                        $out .= var_export($ov, true) . ';' . $newLine;
                    }

                }
                $out .= '}';
                break;
        }
        if ($return === true) {
            return $out;
        } else {
            echo $out;
        }
    }                // 所有输出都是字符串 make sure all concat part are string

    // @todo
    function is_valid_email($email, $test_mx = false)
    { /* checks if email address is valid */
        if (eregi("^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email))
            if ($test_mx) {
                list($username, $domain) = split("@", $email);
                return getmxrr($domain, $mxrecords);
            } else
                return true;
        else
            return false;
    }


    // @todo 思考如何远程 debug, 想想困难有哪些?
//  看不到代码
//  操作不太方便
//  出于代码的保密性,代码不能上传到远程,代码片段有不利于调试
    // @todo 回头测试,并将其移动到合适的位置
    protected function createPatternFromFormat($string)
    {
        $string = str_replace(
            [
                '%e',
                '%s',
                '%S',
                '%a',
                '%A',
                '%w',
                '%i',
                '%d',
                '%x',
                '%f',
                '%c'
            ],
            [
                '\\' . DIRECTORY_SEPARATOR,
                '[^\r\n]+',
                '[^\r\n]*',
                '.+',
                '.*',
                '\s*',
                '[+-]?\d+',
                '\d+',
                '[0-9a-fA-F]+',
                '[+-]?\.?\d+\.?\d*(?:[Ee][+-]?\d+)?',
                '.'
            ],
            preg_quote($string, '/')
        );

        return '/^' . $string . '$/s';
    }

// @todo version compare 函数 http://php.net/manual/en/function.version-compare.php
}