<?php
/**
 * @todo 为Log类写完整的测试案例
 */

namespace play\log;

use app\exception\ConfigError;
use app\exception\FilePermissionError;

class BaseLogger
{
    public static $format = '{DateTime} {LogType} message:{Message} Context:{Context} callplace:{CallPlace}';

    protected static $instance;

    // log 分批写入文件
    protected $batchSize = 20;

    protected static $logType = [
        'request' => [],
        'base' => [],
        'error' => [],
        'sql' => [],
        'custom' => [],
    ];

    protected static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }


    protected static $contents = [];

    protected static $file;

    public function bootstrap()
    {
        register_shutdown_function([self::class, 'writeFile']);
    }


    public static function writeFile()
    {
        foreach (self::$logType as $type => $logs) {
            if (!empty($logs)) {
                $dir = join(DIRECTORY_SEPARATOR, [APP_BASE_PATH, 'storage', 'logs']);
                if (!is_dir($dir)) {
                    throw new FilePermissionError('directory:[' . $dir .
                        '] should exists and writeble, it can not be created automatically!', 500);
                }
                $file = $dir . DIRECTORY_SEPARATOR . join('.', [$type, date('Ymd'), 'log']);
                $fp = fopen($file, 'a+');
                if ($fp) {
                    foreach ($logs as $result) {
                        $result = str_replace(PHP_EOL,'\n', $result);
                        fwrite($fp, $result . PHP_EOL);
                    }
                    fclose($fp);
                }
                // 重置对应类型log
                self::$logType[$type] = [];
            }
        }
    }

    public function getDateTime()
    {
        return date('Y-m-d H:i:s');
    }

    public function getMessage($message)
    {
        if (is_bool($message)) {
            $message = $message === true ? 'boolean(true)' : 'boolean(false)';
        } elseif (is_scalar($message)) { // double, int and string, just convert to string and remove PHP_EOL
            $message = str_replace(PHP_EOL, ' ', (string)$message);
        } elseif (is_object($message)) {
            if (is_subclass_of($message, \JsonSerializable::class)) {
                /**
                 * @var $message \JsonSerializable
                 */
                $message = $message->jsonSerialize();
            } elseif (method_exists($message, '__tostring')) {
                $message = str_replace(PHP_EOL, ' ', $message->__tostirng());
            } else {
                $message = json_decode($message, JSON_UNESCAPED_UNICODE);
            }
        } elseif (is_resource($message)) {
            $message = '<resource not serializable>';
        } else {
            $message = json_encode($message, JSON_UNESCAPED_UNICODE);
        }
        return $message;
    }

    /**
     * @param array $context
     * @return string
     *
     * 递归实现content记录
     */
    public function getContext(array $context)
    {
        $replacements = [];
        foreach ($context as $key => $val) {
            if (is_null($val) || is_scalar($val) || (is_object($val) && method_exists($val, "__toString"))) {
                $replacements['{' . $key . '}'] = $val;
            } elseif (is_object($val)) {
                $replacements['{' . $key . '}'] = '[object ' . get_class($val) . ']';
            } else {
                $replacements['{' . $key . '}'] = '[' . gettype($val) . ']';
            }
        }
        return json_encode($replacements);
    }

    /**
     * @param string $LogType
     * @return string
     */
    public function getLogType(string $LogType)
    {
        return $LogType;
    }

    /**
     * 获取调用地点
     * @return string
     */
    protected function getCallPlace()
    {
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8);
        $file = '';
        $line = '';
        foreach ($stack as $key => $trace) {
            if (isset($trace['file'])) {
                if ($trace['file'] === __FILE__) {
                    continue;
                }
                if (strpos($trace['file'], 'play/db/Query.php')) {
                    continue;
                }
            }
            if (empty($file)) {
                $file = isset($trace['file']) ? $trace['file'] : $file;
                $line = isset($trace['line']) ? $trace['line'] : $line;
            }
        }
        return $file . ':' . $line;
    }

    public function write(string $logType, string $message, array $context = [])
    {
        $arrFormat = explode('{', self::$format);
        $replace = [];
        foreach ($arrFormat as $split) {
            if (!strpos($split, '}')) {
                continue;
            }
            // 街区上方法名称
            $split = ucfirst(substr($split, 0, strpos($split, '}')));
            switch ($split) {
                case 'DateTime':
                    $part = $this->getDateTime();
                    break;
                case 'LogType':
                    $part = $this->getLogType($logType);
                    break;
                case 'CallPlace':
                    $part = $this->getCallPlace();
                    break;
                case 'Message':
                    $part = $this->getMessage($message);
                    break;
                case 'Context':
                    $part = $this->getContext($context);
                    break;
                default:
                    throw new ConfigError('不支持的log消息组成部分:' . $split);
            }
            $replace['{' . $split . '}'] = $part;
        }
        $result = strtr($message, $replace);
        $logType = isset(self::$logType[$logType]) ? $logType : 'base';
        self::$logType[$logType][] = $result;
        // 分批写入日志,避免在常驻进程等情况下占用过高内存
        if (count(self::$logType[$logType]) >= $this->batchSize) {
            self::writeFile();
        }
    }

    /**
     * @param $message
     * @param $parameters
     * @throws ConfigError
     */
    public static function __callstatic($logType, $parameters)
    {
        $message = $parameters[0];
        $context = isset($parameters[1]) ? $parameters[1] : [];
        self::getInstance()->write($logType, $message, $context);
    }
}
