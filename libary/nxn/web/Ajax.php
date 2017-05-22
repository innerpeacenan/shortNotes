<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/14/17
 * Time: 8:29 PM
 */
namespace nxn\web;

/**
 * Class Ajax
 * @package nxn\web
 * User: xiaoning nan
 * Date: 2017-05-{14}
 * Time: xx:xx
 * Description: description
 */
class Ajax
{
    /**
     * @access
     * @return string
     */
    public static function json($status, $data = [], $message = '')
    {
        // 返回JSON数据格式到客户端 包含状态信息
        header('Content-Type:application/json; charset=utf-8');
        $json = json_encode(['status' => $status, 'data' => $data, 'msg' => $message]);
        echo $json;
        exit();
    }
}