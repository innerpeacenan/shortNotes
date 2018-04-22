<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/13/17
 * Time: 12:27 PM
 */
namespace nxn;

defined('N_BASE') or define('N_BASE', __DIR__);

class Autoload
{
    /**
     * @var array
     * 按照给定的一级命名空间和对应的目录,作为自动加载类时候的相对地址
     * An associative array where the key is a namespace prefix (prefix end with slash '/') and
     * the value is the  base directories for classes in that namespace
     */
	public static $classMap = [
		'nxn' => N_BASE, 
		'n' => N_APPLICATION,
		'tests' => N_APPLICATION . '/tests',
	];

    public static function autoload($className)
    {
        if (isset(static::$classMap[$className])) {
            $classFile = static::$classMap[$className];
        } elseif (($pos = strpos($className, '\\')) !== false) {
            $name = substr($className, 0, $pos);//note that  the second param is length
            if (isset(self::$classMap[$name])) {
                $restPart = substr($className, $pos);
                $classFile = self::$classMap[$name] . str_replace('\\', '/', $restPart) . '.php';
            } else {
                $classFile = N_APPLICATION . '/' . str_replace('\\', '/', $className) . '.php';
            }
        }else{
            // auto load should do nothing more than its responsibility
           //throw new \Exception("class $className does not exist!");
        }

        if (isset($classFile) && is_file($classFile)) {
            include($classFile);
            return true;
        } else {
            //throw new \Exception("Unable to find file: '$classFile' to autoload class: '$className'' ");
        }

    }

    /**
     * @param string $full_name_space
     * @param string $base_dir based directory path
     * @return void
     * Description:
     */
    public static function addClassMap($full_name_space, $base_dir)
    {
        //normalize namespace prefix
        $full_name_space = trim($full_name_space, '\\') . '\\';   //这里不能家开头的'\',函数被调用的时候,类没有'\',需要进一步弄明白
        // normalize the base directory with a trailing separator
        $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        // initialize the namespace prefix array
        if (isset(self::$classMap[$full_name_space]) === false) {
            self::$classMap[$full_name_space] = $base_dir;
        }
    }
}

spl_autoload_register(['nxn\\Autoload', 'autoload'], true);
