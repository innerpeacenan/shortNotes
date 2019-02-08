<?php

namespace play\validate;

/**
 * 接口使用说明
 */
class Validate implements ValidateInterface
{
    /**
     * @var array
     *
     * ['property' => ['ruleName' => array_of_rule_params],
     * such as ['age' => ['int' => [], 'between' => [18,30]]]
     */
    protected $attributeAndRules = [];

    /**
     * @var array  ['ruleName' => callable]
     */
    protected $rulesMap = [];

    protected $messageMap = [];

    protected $data = [];

    protected $errors = [];

    // 字段名称和含义的描述
    public $attributeDesction;

    public function setRules($attributeAndRule)
    {
        $this->attributeAndRules = $attributeAndRule;
        return $this;
    }


    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function fails()
    {
        return $this->errors;
    }


    public function failed()
    {
        foreach ($this->attributeAndRules as $attribute => $rules) {
            foreach ($rules as $ruleName => $ruleParams) {
                $callback = [$this, 'validate' . $ruleName];

                if (isset($this->rulesMap[$ruleName]) && is_callable($this->rulesMap[$ruleName])) {
                    $callback = $this->rulesMap[$ruleName];
                }
                if (!is_callable($callback)) {
                    throw new \Exception(json_encode($this->rulesMap[$ruleName]) . $ruleName . ' should has an related callable');
                }
                $value = $this->getValue($attribute);
                if (!call_user_func($callback, $attribute, $value, $ruleParams, $this->data)) {
                    // 对峙进行描述(注册一个描述错误的回调函数)
                    if (is_null($this->messageMap[$ruleName])) {
                        throw new \Exception('default message on error about ' . $ruleName . ' not present');
                    }
                    $message = $this->messageMap[$ruleName];
                    if (is_callable($message)) {
                        $message = call_user_func($this->messageMap[$ruleName], $value, $ruleParams, $this->data);
                    } elseif (is_string($this->messageMap[$ruleName])) {
                        $message = is_callable($value) ? $value . $message : json_encode($value) . $message;

                    }
                    $this->setError($ruleName, $message);
                    return true;
                };
            }
        }
        return false;
    }

    /**
     * 目前各种方法自定义错误不太方便
     */
    protected function setError($ruleName, $msg)
    {
        $this->errors[$ruleName][] = $msg;
    }

    /**
     * @return array
     */
    protected function getValue($attribue)
    {
        return isset($this->data[$attribue]) ? $this->data[$attribue] : null;
    }

    public function validateRequired($attribute, $value)
    {
        $this->messageMap['required'] = $attribute . ' is required';
        if (is_null($value) || (is_string($value) && '' === trim($value)) && (is_array($value) && empty($value))) {
            return false;
        }
        return true;
    }

    protected function valueToString($value)
    {
        if (is_scalar($value)) {
            return $value;
        } else {
            return json_encode($value);
        }
    }

    public function getAttributeDesciption($attribute)
    {
        return isset($this->attributeDesction[$attribute]) ? $this->attributeDesction[$attribute] : $attribute;
    }


    // 有问题,但是先凑合这用吧
    public function isEmpty($value){
        if(is_null($value)){
            return true;
        }
        if(is_string($value) && ('' === trim($value))){
            return true;
        }
        if(is_array($value)){
            // 深埋bug
            $value = array_map('trim', $value);
            foreach ($value as $item){
                if($item !== ''){
                    return false;
                }
            }
        }
        return true;
    }

    public function validateInt($attribute, $value)
    {
        $this->messageMap['int'] = $this->getAttributeDesciption($attribute) . ' value ' . $this->valueToString($value) . ' should be int';
        if('' === trim($value)){
            return true;
        }
        // 整形的校验
        $result = is_null($value) || filter_var($value, FILTER_VALIDATE_INT) !== false;

        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * 到现在可以支持自定义错误信息了
     */
    function validateInArray($attribute, $value, $ruleParams)
    {
        if (!isset($ruleParams[0])) {
            throw new \Exception('the first element of ruleParams must be set');
        }
        if (!is_array($ruleParams[0])) {
            throw new \Exception('the first element of ruleParams must be array');
        }


        $this->messageMap['inArray'] = function ($value, $ruleParams) {
            $message = is_scalar($value) ? $value : json_encode($value);
            $message .= ' must in array:' . json_encode($ruleParams[0]);
            return $message;
        };

        if (is_null($value)) {
            return true;
        }

        if (is_array($value) && (!empty(array_diff($value, $ruleParams[0])))) {
            return false;
        }
        if (is_string($value) && (!in_array($value, $ruleParams[0]))) {
            return false;
        }
        if (is_null($value)) {
            throw new \Exception('value is null');
        }
        return true;
    }


    public function validateSometimes()
    {
        return true;
    }


    // 用于扩展规则, callBack 最多包含死个参数, 依次是 $attribute, $value, $ruleParams, $theWholeInput
    public function extend($ruleName, $callBack, $msg)
    {
        $this->rulesMap[$ruleName] = $callBack;
        $this->messageMap[$ruleName] = $msg;
    }
}