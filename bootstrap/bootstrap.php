<?php
// 常量,配置文件,
defined('APP_BASE_PATH') or define('APP_BASE_PATH', __DIR__ . '/../');

try {
    $app = new \play\Application();
} catch (Throwable $e) {
    var_export($e->getMessage());die();
    \play\web\Ajax::json($e->getCode(), $e->getTrace(), $e->getMessage());
    if ('cli' === PHP_SAPI) {
        throw $e;
    } else {
        throw $e;
        \Log::errorOnstart($e->__toString());
    }
    exit($e->getCode());
}







