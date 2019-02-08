<?php
require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../bootstrap/bootstrap.php');

/**
 * @var $kernel \play\web\Kernel::class
 */
try {
    $kernel = $app->make('HttpKernel');
    $kernel->handle();
} catch (Throwable $e) {
    if ('cli' === PHP_SAPI) {
        echo $e->getTraceAsString();
    } else {
        \Log::errorOnstart($e->__toString());
    }
    exit($e->getCode());
}
