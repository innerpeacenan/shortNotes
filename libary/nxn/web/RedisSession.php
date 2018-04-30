<?php
namespace nxn\web;

class RedisSession implements SessionDriverInterface {
    public $ip;
    public $port;
    public $redis;

    public function __construct(){
        $redisConf = \N::$app->conf['redis'];
        $this->ip = $redisConf['ip'];
        $this->port = $redisConf['port'];
        $this->redis = new \Redis();
        // expire_time 设置, 之前因此出发过一个bug
        $this->sessionExpireTime = 7 * 24 * 3600;
        $this->redis->connect($this->ip, $this->port);
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
