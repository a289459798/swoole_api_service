<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/21
 * Time: 22:39
 */

namespace Bijou\Pool;


class PoolManager
{
    private $pool;


    public function __construct()
    {
        $this->pool = [];
    }

    public function addDriver($name, IPool $driver)
    {

        $this->pool[$name] = new Pool($driver);
    }

    public function driver($name)
    {
        if (isset($this->pool[$name])) {
            return $this->pool[$name];
        }
        return false;
    }
}