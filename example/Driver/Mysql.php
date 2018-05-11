<?php

/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/22
 * Time: 00:04
 */

namespace Bijou\Example\Driver;

use Bijou\Pool\IPool;

class Mysql implements IPool
{

    public function sleep($size)
    {
        for($i = 0; $i < $size; $i++) {

    }
        return 'mysql 测试连接池';
    }

    /**
     * 释放资源
     * @return mixed
     */
    public function release()
    {
    }

    /**
     * @return mixed
     */
    public function __clone()
    {
    }

    /**
     * 每个进程允许的最大空闲连接数
     * @return int
     */
    public function allowPoolSize()
    {
        return 20;
    }

    /**
     * 每个进程运行的最大连接数
     * @return int
     */
    public function maxPoolSize()
    {
        return 10;
    }
}