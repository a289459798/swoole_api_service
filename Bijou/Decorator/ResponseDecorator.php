<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 11:40
 */

namespace Bijou\Decorator;


abstract class ResponseDecorator extends Decorator
{
    /**
     * 自定义response 的数据格式
     * @param $data
     * @return mixed
     */
    abstract public function format($data);
}