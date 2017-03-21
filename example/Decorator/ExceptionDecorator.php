<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 11:40
 */

namespace Bijou\Example\Decorator;


class ExceptionDecorator extends \Bijou\Decorator\ExceptionDecorator
{

    /**
     * @param \Throwable $throwable
     * @return Array
     */
    public function throwException(\Throwable $throwable)
    {
        echo 'file:' . $throwable->getFile() . '--line:' . $throwable->getLine();
        return [
            'code' => '自定义提示代码/默认代码:' . $throwable->getCode(),
            'message' => '自定义提示错误信息/默认信息:' . $throwable->getMessage(),
            'file' =>  $throwable->getFile(),
            'line' =>  $throwable->getLine(),
        ];
    }
}