<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 11:40
 */

namespace Bijou\Decorator;


abstract class RunTimeDecorator extends Decorator
{
    /**
     * 请求开始之前回调，可验证请求的安全性，返回true 正常请求，否则 请求终止，并输出返回内容
     * @return bool
     */
    abstract public function requestStart();

    /**
     * 请求完成之后回调之后回调
     * @return mixed
     */
    abstract public function requestEnd();
}