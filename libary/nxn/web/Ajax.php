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
            header('Content-Encoding: deflate');
            if (!getenv('N_TEST')) {
                $gzip = gzdeflate(json_encode($row));
                \Log::info('before_compress_content_length:' . mb_strlen(json_encode($row, 256), '8bit') . 
                ',after_gzip_content_length:' . mb_strlen($gzip, '8bit') . 
                'compress_rate:' . 100 * mb_strlen($gzip, '8bit') / mb_strlen(json_encode($row, 256), '8bit'));
                echo $gzip;
                exit();
            }
            return "";
        } else {
            throw new \Exception('head has already set!');
        }
    }
}
