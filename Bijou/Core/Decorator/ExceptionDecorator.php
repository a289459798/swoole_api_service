<?php

namespace Bijou\Core\Decorator;

use Bijou\Components\Http\Request;
use Bijou\Components\Http\Response;

abstract class ExceptionDecorator extends Decorator
{
    /**
     * 重写该方法，可自定义错误以及记录错误日志等操作
     * @param Request $request
     * @param Response $response
     * @param \Throwable $throwable
     * @return array
     */
    abstract function throwException(Request $request, Response $response, \Throwable $throwable);
}