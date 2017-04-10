<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 11:40
 */

namespace Bijou\Example\Decorator;


use Bijou\Http\Request;

class ExceptionDecorator extends \Bijou\Decorator\ExceptionDecorator
{

    /**
     * @param Request $request
     * @param \Throwable $throwable
     * @return Array
     */
    public function throwException(Request $request, \Throwable $throwable)
    {
        return [
            'code' => $throwable->getCode(),
            'message' => $throwable->getMessage(),
            'file' =>  $throwable->getFile(),
            'line' =>  $throwable->getLine(),
        ];
    }
}