<?php
function ve($var)
{
    header('Content-type: text/html; charset=utf-8');
    echo "<pre>";
    var_export($var);
    echo "</pre>";
}

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

function t(string $k)
{
    if (isset($GLOBALS['PHP_UNIT_TEST'][$k])) {
        return $GLOBALS['PHP_UNIT_TEST'][$k];
    } else {
        return null;
    }
}
