<?php

namespace nxn\log;

class BaseLogger
{
    public static $format = ' {DateTime} errorType:{ErrorType} callplace:{CallPlace} message:{Message}';
    private static $request;

    public $message = '';

    protected static $contents = [];

    protected static $file;


    public static function writeFile()
    {
        if (!empty(self::$contents)) {
            $dir = N_APPLICATION . '/storage/logs/base/';
            if (!is_dir($dir)) {
                throw new \Exception('directory:[' . $dir .
                    '] should exists and writeble, it can not be created automatically!', 500);
            }
            $file = $dir . date('Ymd') . '.log';
            self::$file = fopen($file, 'a+');
            foreach (self::$contents as $result) {
                fwrite(self::$file, $result . PHP_EOL);
            }
            fclose(self::$file);
        }

        if (!empty(self::$request)) {
            $dir = N_APPLICATION . '/storage/logs/request/';
            if (!is_dir($dir)) {
                throw new \Exception('directory:[' . $dir .
                    '] should exists and writeble, it can not be created automatically!', 500);
            }
            $file = $dir . date('Ymd') . '.log';
            self::$file = fopen($file, 'a+');
            foreach (self::$request as $result) {
                fwrite(self::$file, $result . PHP_EOL);
            }
            fclose(self::$file);
        }

    }

    public function getDateTime()
    {
        return date('Y-m-d H:i:s');
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        if (is_bool($message)) {
            $this->message = $message === true ? 'boolean(true)' : 'boolean(false)';
        } elseif (is_scalar($message)) { // double, int and string, just convert to string and remove PHP_EOL
            $this->message = str_replace(PHP_EOL, ' ', (string)$message);
        } elseif (is_object($message) && method_exists($message, '__tostring')) {
            $this->message = str_replace(PHP_EOL, ' ', $message->__tostirng());
        } elseif (is_resource($message)) {
            $this->message = '';
        } else {
            $this->message = json_encode($message, JSON_UNESCAPED_UNICODE);
        }

    }

    public function getErrorType($errorType)
    {
        return $errorType;
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
        $class = '';
        $method = '';
        foreach ($stack as $key => $trace) {
            if (isset($trace['file'])) {
                if ($trace['file'] === __FILE__) {
                    continue;
                }
                if (strpos($trace['file'], 'nxn/db/Query.php')) {
                    continue;
                }
            }
            if(empty($file)){
                $file = isset($trace['file']) ? $trace['file'] : $file;
                $line = isset($trace['line']) ? $trace['line'] : $line;
            }
        }
        return $file . ':' . $line;
    }

    public function write($message, $errorType)
    {
        $this->setMessage($message);
        $message = self::$format;
        $arrFormat = explode('{', $message);
        $replace = [];
        foreach ($arrFormat as $split) {
            if (!strpos($split, '}')) {
                continue;
            }
            $split = substr($split, 0, strpos($split, '}'));
            $method = 'get' . ucfirst($split);
            if (method_exists($this, $method)) {
                if ($method === 'getErrorType') {
                    $replace['{' . $split . '}'] = $this->getErrorType($errorType);
                } else {
                    $replace['{' . $split . '}'] = $this->{$method}();
                }
            }
        }
        $result = strtr($message, $replace);
        if (strtolower($errorType) == 'request') {
            self::$request[] = $result;
        } else {
            self::$contents[] = $result;
        }

    }

    public static function __callstatic($method, $parameters)
    {
        $instance = new static();
        $instance->write(reset($parameters), $method);
    }
}