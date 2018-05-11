<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 11:40
 */

namespace Bijou\Decorator;


use Bijou\Http\Request;
use Bijou\Http\Response;

abstract class ExceptionDecorator extends Decorator
{
    /**
     * 重写该方法，可自定义错误以及记录错误日志等操作
     * @param Request $request
     * @param Response $response
     * @param \Throwable $throwable
     * @return Array
     */
    abstract function throwException(Request $request, Response $response, \Throwable $throwable);
}