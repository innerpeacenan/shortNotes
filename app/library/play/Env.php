<?php
/**
 * Created by PhpStorm.
 * User: nanxiaoning
 * Date: 1/26/2019
 * Time: 9:43 PM
 */

namespace play;


class Env
{
    /**
     * @throws \app\exception\ConfigError
     */
    public function bootstrap()
    {
        //environment
        $environmentVariables = require(APP_BASE_PATH . '.env');
        if (!is_array($environmentVariables)) {
            throw new \app\exception\ConfigError("env file should return an array");
        }
        foreach ($environmentVariables as $name => $value) {
            putenv($name . '=' . $value);
        }
    }
}