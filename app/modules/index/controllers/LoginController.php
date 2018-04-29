<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/14/17
 * Time: 1:19 PM
 */
namespace n\modules\index\controllers;

use n\models\Users;
use nxn\web\Controller;
use nxn\web\SessionManager;

class LoginController extends Controller
{
    /**
     * @access
     * @return void
     * Description:
     * 测试通过,header() 方法对页面重新定向
     * 删除 cookie 测试通过
     *
     */
    public function getIn()
    {
        require(N_APPLICATION . '/app/modules/index/views/login.php');
    }

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
        // generate auto_generated_session
        $sessionManger = new SessionManager();
        if ($sessionManger->name !== session_name()) {
            session_name($sessionManger->name);
        }

        if($user = Users::userByName($_REQUEST['name']) and $_REQUEST['passwd'] == $user['password']) {
            session_regenerate_id();
            $_SESSION['user_id'] = $user['id'];
            session_set_cookie_params(7 * 24 * 3600, '/', gethostname(), false, true);
            if (isset($_SESSION['HTTP_REFERER']) and $_SESSION['HTTP_REFERER'] !== '/login/in') {
                header("Location: http://{$_SERVER['HTTP_HOST']}{$_SESSION['HTTP_REFERER']}");
            } else {
                header("Location: http://{$_SERVER['HTTP_HOST']}");
            }
        } else {
            if (empty($user)) {
                $_REQUEST['error']['name'] = "user name is not correct";
            } else if ($_REQUEST['passwd'] != $user['password']) {
                $_REQUEST['error']['passwd'] = 'password not correct';
            }
            // 否则，登录失败，重新登录
            $this->getIn();
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
