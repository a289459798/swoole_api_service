<?php

namespace Bijou\Manager\Pool;

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