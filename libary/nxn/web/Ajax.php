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
    public static function json($status, $data = [], $message = '', $responseCode = 200, $header = [])
    {
        $row = ['status' => $status, 'data' => $data, 'msg' => $message];
        // 返回JSON数据格式到客户端 包含状态信息
        if (!headers_sent()) {
            http_response_code($responseCode);
            header('Content-Type:application/json; charset=utf-8');
            header('Content-Encoding: gzip');
//   傻了,我说之前怎么没有输出,都没有 echo ,怎么会有输出呢?哈哈
            if (!getenv('N_TEST')) {
                echo gzencode(json_encode($row));
                exit();
            }
            return "";
        } else {
            throw new \Exception('head has already set!');
        }
    }
}
