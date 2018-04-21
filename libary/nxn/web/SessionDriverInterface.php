<?php
namespace nxn\web;

interface SessionDriverInterface {
    public function open();

    public function close();

    public function read($key);

    public function write($key, $value);

    public function destroy($id);

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
    public function gc();

    public function __destruct();

}
