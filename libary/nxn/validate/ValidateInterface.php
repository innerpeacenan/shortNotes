<?php

namespace nxn\validate;

interface validateInterface
{
    public function setData(array $data);

    // 为某个字段添加校验规则
    public function setRules($attributeAndRule);

    // 检查是否失败
    public function failed();

    // 返回失败信息
    public function fails();

}