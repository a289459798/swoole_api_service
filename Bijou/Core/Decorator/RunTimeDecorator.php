<?php

namespace Bijou\Core\Decorator;

use Bijou\Components\Http\Request;

abstract class RunTimeDecorator extends Decorator
{
    /**
     * 请求开始之前回调，可验证请求的安全性，返回true 正常请求，否则 请求终止，并输出返回内容
     * @param Request $request
     * @return bool
     */
    abstract public function requestStart(Request $request);

    /**
     * 请求完成之后回调之后回调
     * @param Request $request
     * @param $data
     * @return mixed
     */
    abstract public function requestEnd(Request $request, $data);

    /**
     * 自定义response 的数据格式
     * @param $data
     * @return mixed
     */
    abstract public function responseFormat($data);
}