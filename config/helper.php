<?php

if (getenv('N_DEBUG')) {
// enable error reportings
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    function ve($var, $exit = true)
    {
        header('Content-type: text/html; charset=utf-8');
        echo "<pre>";
        var_export($var);
        echo "</pre>";
        if ($exit) {
            exit();
        }
    }
} else {
    function ve($var)
    {
    }
}

if (getenv('N_TEST')) {
    function l(array $kv)
    {
        foreach ($kv as $k => $v) {
            if (!is_resource($v)) {
                $GLOBALS['PHP_UNIT_TEST'][$k] = unserialize(serialize($v));
            } else {
                throw new Exception('resource could not be logged!');
            }
        }
    }
} else {
    function l(array $kv)
    {
    }
}

function t(string $k)
{
    if (isset($GLOBALS['PHP_UNIT_TEST'][$k])) {
        return $GLOBALS['PHP_UNIT_TEST'][$k];
    } else {
        return null;
    }
}


class_alias('nxn\log\BaseLogger', 'Log');
register_shutdown_function(['nxn\log\BaseLogger', 'writeFile']);
