<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/13/17
 * Time: 9:12 PM
 */
namespace nxn\web;

/**
 * Class AuthController
 * @package libary\nxn\web
 * User: xiaoning nan
 * Date: 2017-08-{13}
 * Time: xx:xx
 * Description: description
 */
class AuthController extends Controller
{
    public function beforeAction()
    {// 这里实现登录控制的功能
        // client ip address
        $_SERVER['REMOTE_ADDR'];
        // server addr
        $_SERVER['SERVER_ADDR'];

        // 这只session_name
        $sessionName = 'makeLifeEasier';
        if ($sessionName !== session_name()) {
            session_name($sessionName);
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        // 检测cookie 对应的字段，没有，跳转到登录页
        // 没有 cookie,没有 session
        if (isset($_COOKIE[$sessionName], $_SESSION['user_id']) && $_COOKIE[$sessionName] == session_id()) {
            // 正常登录 pass
        } else {
            // HTTP_HOST such as： 'www.note.git'
            // bug: status 不能返回 200, 否则页面有停留在了最初访问的页面了
            $_SESSION['HTTP_REFERER'] = $_SERVER['REQUEST_URI'];
            header("Location: http://{$_SERVER['HTTP_HOST']}/login/in");
        }
    }
}