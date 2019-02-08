<?php
/**
 * Created by PhpStorm.
 * User: nanxiaoning
 * Date: 12/22/2018
 * Time: 2:03 AM
 */

namespace app\exception;


use Throwable;

class ConfigError extends \Exception
{
    public function __construct(string $message = "", int $code = 501, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}