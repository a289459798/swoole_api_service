<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/21
 * Time: 22:39
 */

namespace Bijou\Pool;

class Pool
{

    private $allowPoolSize;
    private $maxPoolSize;
    private $freePool;
    private $freePoolSize;
    private $busyPool;
    private $busyPoolSize;
    private $driver;

    public function __construct(IPool $driver)
    {

        $this->allowPoolSize = $driver->allowPoolSize();
        $this->maxPoolSize = $driver->maxPoolSize();
        $this->freePool = new \SplQueue();
        $this->busyPool = new \SplQueue();
        $this->freePoolSize = 0;
        $this->busyPoolSize = 0;
        $this->driver = $driver;
    }

    private function doTask($driver, $name, $arguments)
    {
        $this->busyPool->enqueue($driver);
        $this->busyPoolSize++;
        $this->freePoolSize > 0 && $this->freePoolSize--;
        $return = call_user_func_array([$driver, $name], $arguments);
        $this->release($driver);

        return $return;
    }

    /**
     * 释放资源
     * @param $driver
     */
    private function release($driver)
    {
        $this->busyPoolSize > 0 && $this->busyPoolSize--;
        if ($this->allowPoolSize > $this->freePoolSize) {
            $this->freePool->enqueue($driver);
            $this->freePoolSize++;
            $driver->release();
        }
    }

    public function __call($name, $arguments)
    {
        if ($this->freePoolSize > 0) {
            var_dump("存在空闲连接");
            // 存在闲着
            $driver = $this->freePool->dequeue();
            return $this->doTask($driver, $name, $arguments);
        } else if ($this->busyPoolSize < $this->maxPoolSize) {
            var_dump("连接都在繁忙状态");
            $driver = clone $this->driver;
            return $this->doTask($driver, $name, $arguments);
        } else {
            // 超过最大连接池
            return false;
        }
    }
}