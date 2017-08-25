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
    /**
     * @var \Redis
     */
    public $redis;
    /**
     * @var int the unit is seconds
     */
    public $sessionExpireTime = 24 * 3600 * 7;

    public function __construct()
    {
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $retval = session_set_save_handler([$this, 'open'], [$this, 'close'], [$this, 'read'], [$this, 'write'], [$this, 'destroy'], [$this, 'gc']);
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

    public function open()
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($key)
    {
        $value = $this->redis->get($key);

        if ($value) {
            return $value;
        }
        return '';
    }

    public function write($key, $value)
    {
        if ($this->redis->set($key, $value)) {
            $this->redis->expire($key, $this->sessionExpireTime);
            return true;
        }
        return false;
    }

    public function destroy($id)
    {
        if ($this->redis->del($id)) {
            return true;
        }
        return false;
    }

    /**
     * @access
     * @return bool
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     *
     *
     * Description:
     * PHP 执行垃圾回收时候触发该函数
     * request params:
     * name |meaning
     * ---|---
     *
     *
     *
     */
    public function gc()
    {
        return true;
    }

    public function __destruct()
    {
        session_write_close();
    }

}