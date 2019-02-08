<?php
/**
 * Created by PhpStorm.
 * User: nanxiaoning
 * Date: 1/26/2019
 * Time: 9:41 PM
 */

namespace play;


class Config
{
    /**
     * @var string|null
     */
    protected static $basePath;

    public static $ini = [];

    /**
     * 设置默认路径
     */
    public function bootstrap()
    {
        self::$basePath = APP_BASE_PATH . 'config' . DIRECTORY_SEPARATOR;
    }

    /**
     * /作为文件路径分隔符
     * main/db.shareParam
     */
    public static function get(string $str, $default = null)
    {
        if (!isset(self::$ini[$str])) {
            self::$ini[$str] = self::internalGet($str, $default);
        }
        return self::$ini[$str];
    }


    /**
     * @param string $str
     * @param null $default
     * @return mixed|null|void
     * @throws \Throwable
     * @nxn 给各种找不到的值加log
     */
    protected static function internalGet(string $str, $default = null)
    {
        if (isset($default)) {
            if ($default instanceof \Throwable) {
                throw $default;
            } elseif ($default instanceof \Closure) {
                $default = $default($str);
            } else {
                $default;
            }
        }
        // 如果找到了结果,则放到静态变量里边
        $pathEndPos = strrpos($str, '/');
        // 找不到,则认为是文件路径
        if (false === $pathEndPos) {
            $file = self::$basePath . $str . '.php';
        } else {
            $file = self::$basePath . substr($str, 0, $pathEndPos) . '.php';
        }

        if (!file_exists($file)) {
            return $default;
        }
        // 保证同一个文件,只会被require一次,用以提升性能
        if (isset(self::$ini[$file])) {
            $fileContent = self::$ini[$file];
        } else {
            $fileContent = includeFile($file);
        }

        if (!is_array($fileContent)) {
            return $default;
        }
        if (false === $pathEndPos) {
            return $fileContent;
        }
        // 数据中的查找路径 db.shareParam
        $keyPath = substr($str, $pathEndPos + 1);
        // 按照计划
        $parts = explode('.', $keyPath);
        $content = $fileContent;
        while ($key = array_shift($parts)) {
            if (!isset($content[$key])) {
                return $default;
            }
            if (!is_array($content[$key])) {
                // 说明还有剩余部分未匹配上
                if (!empty($parts)) {
                    return $default;
                }
            }
            // 全部匹配完了
            if (empty($parts)) {
                return $content[$key];
            }
            $content = $content[$key];
        }
        return $default;
    }

}

/**
 * Scope isolated include.
 *
 * Prevents access to $this/self from included files.
 */
function includeFile(string $file)
{
    return require($file);
}