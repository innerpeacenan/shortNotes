<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/14/17
 * Time: 1:19 PM
 */
namespace n\modules\index\controllers;


class IndexController
{
    public function indexGET(){
        ///home/wwwroot/www.note.com
        require(N_APPLICATION . '/modules/index/views/index.php');

    }
}