<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/21
 * Time: 22:44
 */

namespace Bijou\Interfaces;


interface PoolInterface
{
    /**
     * 每个进程允许的最大空闲连接数
     * @return int
     */
    public function allowPoolSize();

    /**
     * 每个进程运行的最大连接数
     * @return int
     */
    public function maxPoolSize();

    /**
     * 释放资源
     * @return mixed
     */
    public function release();

    /**
     * @return mixed
     */
    public function __clone();
}