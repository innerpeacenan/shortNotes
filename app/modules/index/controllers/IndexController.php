<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/14/17
 * Time: 1:19 PM
 */
namespace n\modules\index\controllers;

use nxn\web\AuthController;

class IndexController extends AuthController
{
    public function indexGET()
    {
        require(N_APPLICATION . '/app/modules/index/views/index.php');
    }
}
