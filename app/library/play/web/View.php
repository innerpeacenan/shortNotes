<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/18/17
 * Time: 9:47 AM
 */
namespace play\web;

/**
 * Class View
 * @package play\web2
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

    public function render($view, $data)
    {
        ob_start();
        ob_implicit_flush(false);
        extract($data, EXTR_OVERWRITE);
        require($view);
        return ob_clean();
    }

    /**
     * @todo
     */
    public function getViewPath(string $view)
    {
        if (trim($view) === '') {
//            \N::$app->view ->viewPath =
        }
        if (strpos($view, '/') === false) {

        }
        return true;
    }

    /**
     * @return string the code file extension (e.g. php, txt)
     */
    public function getType()
    {
        if (($pos = strrpos($this->viewPath, '.')) !== false) {
            return substr($this->viewPath, $pos + 1);
        } else {
            return 'unknown';
        }
    }
}