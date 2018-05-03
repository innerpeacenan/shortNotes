<?php

namespace nxn\log;

class BaseLogger
{
    public static $format = '{DateTime} callplace:{CallPlace} message:{Message}';

    public $message = '';

    protected static $contents = [];

    protected static $file;

    public static function writeFile()
    {
        $file = N_APPLICATION . '/storage/logs/base' . date('Ymd') . '.log';
        if (is_null(self::$file)) {
            self::$file = fopen($file, 'a+');
        }
        foreach (self::$contents as $result){
            fwrite(self::$file, $result . PHP_EOL);
        }
        fclose(self::$file);
    }

    public function setFile($file = null)
    {

    }

    public function getDateTime()
    {
        return date('YmdHis');
    }

    public function getMessage()
    {
        return $this->message;
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
            if ($trace['file'] === __FILE__) {
                continue;
            }
            $file = isset($trace['file']) ? $trace['file'] : $file;
            $line = isset($trace['line']) ? $trace['line'] : $line;
            if (isset($trace['class']) && strpos($trace['class'], 'Yunniao') === false) {
                break;
            }
        }
        return $file . ':' . $line;
    }

    public function write($message)
    {
        $this->message = $message;
        $this->setFile();
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
                $replace['{' . $split . '}'] = $this->{$method}();
            }
        }
        $result = strtr($message, $replace);
        self::$contents[] = $result;
    }

    public static function __callstatic($method, $parameters)
    {
        $instance = new static();
        $instance->write(reset($parameters));
    }
}