<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/13/17
 * Time: 9:12 PM
 */
namespace app\modules\account\controllers;

use play\web\Ajax;
use play\web\Controller;
use play\web\SessionManager;
/**
 * Class AuthController
 * @package library\play\web2
 * User: xiaoning nan
 * Date: 2017-08-13
 * Time: xx:xx
 * Description: description
 */
class AuthController extends Controller
{
    public function beforeAction()
    {
        // 这只session_name
        $sessionName = getenv('SESSION_VALUE');
        if ($sessionName !== session_name()) {
            session_name($sessionName);
        }
        $ok = new SessionManager();
        if (!$ok) throw new \Exception('session manageer error',500);
        // 检测cookie 对应的字段，没有，跳转到登录页
        if (isset($_COOKIE[$sessionName], $_SESSION['user_id']) && $_COOKIE[$sessionName] == session_id()) {
            // 正常登录 pass
        } else {
            // HTTP_HOST such as： 'www.note.git'
            $_SESSION['HTTP_REFERER'] = $_SERVER['REQUEST_URI'];
            throw new \Exception('permission deny!', 403);
        }
    }
}
