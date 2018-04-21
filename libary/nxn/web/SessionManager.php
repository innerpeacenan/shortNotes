<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/24/17
 * Time: 4:37 PM
 */
namespace nxn\web;

/**
 * Class SessionManager
 * @package nxn\web
 * User: xiaoning nan
 * Date: 2017-08-{24}
 * Time: xx:xx
 * Description: description
 */
class SessionManager
{
    public $driver;
    /**
     * @var int the unit is seconds
     */
    public $sessionExpireTime = 24 * 3600 * 7;
    public $name;

    public function __construct()
    {
        $sessionConf = \N::$app->conf['session'];
        $this->driver = $sessionConf['driver'];
        $this->name = $sessionConf['name'];

        if('file' == $sessionConf['driver']){
            //要想使用命名会话，请在调用 session_start() 函数 之前调用 session_name() 函数。
            session_name($this->name);
            session_start();
        }else{
            if('redis' === $this->driver){
                $config = \N::$app->conf['redis'];
            }else{
                throw new \Exception('does not support ' . $this->driver . 'yet');
            }
            $driverClass = __namespace__ . '\\' . ucfirst($this->driver) . 'Session';
            //ve($driverClass);
            if(!isset($config['class'])){
                $config['class'] = $driverClass;
            }
            $sessionDriver = \N::createObject($config);
            $retval = session_set_save_handler(
                 [$sessionDriver , 'open'],
                 [$sessionDriver,'close'],
                 [$sessionDriver, 'read'],
                 [$sessionDriver, 'write'],
                 [$sessionDriver, 'destroy'],
                 [$sessionDriver, 'gc']
            );
            if (true === $retval) {
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    @session_start();
                } else {
                    throw new \Exception('session start error');
                }
            } else {
                throw new \Exception('error on session save handler');
            }
       }
   }

}
