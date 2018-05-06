<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/14/17
 * Time: 1:19 PM
 */
namespace n\modules\index\controllers;

use n\models\Users;
use nxn\web\Ajax;
use nxn\web\Controller;
use nxn\web\SessionManager;

class LoginController extends Controller
{
    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     * Description:
     * request params:
     * name |meaning
     * ---|---
     * name | userName
     * passwd | password
     *
     */
    public function postIn()
    {
        $this->validate();

        $sessionManger = new SessionManager();
        if ($sessionManger->name !== session_name()) {
            session_name($sessionManger->name);
        }

        if($user = Users::userByName($_REQUEST['name']) and $_REQUEST['passwd'] == $user['password']) {
            session_regenerate_id();
            $_SESSION['user_id'] = $user['id'];
            session_set_cookie_params(7 * 24 * 3600, '/', gethostname(), false, true);
            if (isset($_SESSION['HTTP_REFERER']) and $_SESSION['HTTP_REFERER'] !== '/web/page/account/login.html') {
                $redirect = "http://{$_SERVER['HTTP_HOST']}{$_SESSION['HTTP_REFERER']}";
            } else {
                $redirect = "http://{$_SERVER['HTTP_HOST']}";
            }
            Ajax::json(1, ['redirect_to' => $redirect], '进入主页',302);
        } else {
            $err = [];
            if (empty($user)) {
                $err['name'] = "用户名不正确";
            } else if ($_REQUEST['passwd'] != $user['password']) {
                $err['passwd'] = '密码不正确';
            }
            Ajax::json(0, ['errorMsg' => json_encode($err)]);
        }
    }

    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     * Description:
     *
     * @todo validate request by regular expression
     * request params:
     * name |meaning
     * ---|---
     *
     *
     *
     */
    public function validate()
    {

    }
}
