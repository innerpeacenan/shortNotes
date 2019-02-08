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