<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/18/17
 * Time: 9:05 AM
 */
namespace nxn\web;

use TheSeer\Autoload\PathComparator;

class Controller
{
    public function renderView($viewName, array $data)
    {
        return $this->render($data,$type='view',$viewName);
    }

    public function renderJson ($arr){
        return $this->render($arr,$type='json');
    }

    public function render(array $data, $type = 'json', $viewName = null)
    {
        if (N_TEST) return $data;
        switch ($type) {
            case 'json':
                Ajax::json($data);
                break;
            case 'view':
                $view = \N::$app->view;
                return $view->render($viewName,$data);
                break;
        }
    }
}