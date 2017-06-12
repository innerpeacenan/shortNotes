<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/14/17
 * Time: 1:19 PM
 */
namespace n\modules\index\controllers;


use n\models\Items;
use nxn\db\ActiveRecord;
use nxn\web\Response;

class IndexController
{
    public function indexGET()
    {
        ///home/wwwroot/www.note.com
        require(N_APPLICATION . '/modules/index/views/index.php');

    }

    /**
     * @access
     * @return void
     * Description:
     * 测试通过,header() 方法对页面重新定向
     * 删除 cookie 测试通过
     *
     */
    public function testGET()
    {
        var_dump($_SERVER,$_REQUEST,$_GET,$_POST);
//        Response::redirect('/index/index');
//        setcookie('XDEBUG_SESSION','PHPSTORM');
//        var_dump($_COOKIE);
//        setcookie('XDEBUG_SESSION',null);
    }
}