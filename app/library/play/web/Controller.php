<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/18/17
 * Time: 9:05 AM
 */

namespace play\web;

class Controller
{
    public $request;

    public function renderView($viewName, array $data)
    {
        return $this->render($data, $type = 'view', $viewName);
    }

    public function renderJson($arr)
    {
        return $this->render($arr, $type = 'json');
    }

    public function render(array $data, $type = 'json', $viewName = null)
    {
        if (N_TEST) return $data;
        switch ($type) {
            case 'json':
                return Ajax::json($data);
                break;
            case 'view':
                $view = \N::$app->view;
                return $view->render($viewName, $data);
                break;
            default:
                return "";
                break;
        }
    }

    public function beforeAction()
    {
        $this->request = new Request();
    }

    //@TODO 至此,将参数校验的类封装到了 controller 里边, 不过现在只能抛出Ajax类型错误, 这一点目前是不对的
    public function validate($attributesAndRules, $descMap = [])
    {
        if (!isset($this->request)) {
            $this->request = new Request();
        }

        $this->request->validate($attributesAndRules, $descMap);
    }

    // 目前来说,测试还很不充分
    public function extendValidate($ruleName, $callBack, $msg)
    {
        if (!isset($this->request)) {
            $this->request = new Request();
        }
        // 该方法暂时无法设置错误信息
        $this->request->validator->extend($ruleName, $callBack, $msg);
    }
}