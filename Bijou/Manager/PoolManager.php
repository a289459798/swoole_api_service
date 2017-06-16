<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/21
 * Time: 22:39
 */

namespace Bijou\Manager;


use Bijou\Interfaces\PoolInterface;

class PoolManager
{
    private $pool;


    public function __construct()
    {
        $this->pool = [];
    }

    public function addDriver($name, PoolInterface $driver)
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