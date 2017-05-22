<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/18/17
 * Time: 9:47 AM
 */
namespace nxn\web;

/**
 * Class View
 * @package nxn\web
 * User: xiaoning nan
 * Date: 2017-05-{18}
 * Time: xx:xx
 * Description: description
 */
class View
{
    /**
     * @var string | boolean false to disable layout
     */
    public $layout = 'main';

    public $viewPath;

    public function render ($view,$data){
        ob_start();
        ob_implicit_flush(false);
        extract($data,EXTR_OVERWRITE);
        require($view);
        return ob_clean();
    }

    /**
     * @todo
     *
     *
     */
    public function getViewPath(string $view){
        if(trim($view) === '') {
//            \N::$app->view ->viewPath =
        }
        if(strpos($view,'/') === false){

        }
        return true;
    }
}