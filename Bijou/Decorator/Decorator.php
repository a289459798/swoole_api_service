<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 11:39
 */

namespace Bijou\Decorator;


abstract class Decorator
{
    private $api;

    public function setApi($api)
    {
        $this->api = $api;
    }

    public function getApi()
    {
        return $this->api;
    }
}