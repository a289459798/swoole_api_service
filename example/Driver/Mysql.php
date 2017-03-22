<?php

/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/22
 * Time: 00:04
 */

namespace Bijou\Example\Driver;

use Bijou\Interfaces\PoolInterface;

class Mysql implements PoolInterface
{

    public function sleep($size)
    {
        for($i = 0; $i < $size; $i++) {

    }
        return 'mysql 测试连接池';
    }

    public function allowPoolSize()
    {

        return 10;
    }

    public function maxPoolSize()
    {
        return 100;
    }
}