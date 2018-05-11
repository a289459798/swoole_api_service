<?php

namespace Bijou\Components\CoRedis;

use Swoole\Coroutine\Redis;

/**
 * Class AsyncRedis
 * 基于redis官方提供的hiredis库实现
 * 编译安装hiredis,下载hiredis源码(https://github.com/redis/hiredis/releases)
 * make -j
 * sudo make install
 * sudo ldconfig
 * -------------------------------------------------------
 * 编译swoole时，在configure指令中加入--enable-async-redis
 * @package Spf\Library\Swoole\Common
 */
class CoRedis
{
    /**
     * @var string
     */
    private $host = '127.0.0.1';

    /**
     * @var int|string
     */
    private $port = 6379;

    /**
     * @var int
     */
    private $timeout = 0;

    /**
     * @var int
     */
    private $db = 0;

    /**
     * @var string
     */
    private $pass = '';

    /**
     * @var Redis
     */
    private $redis;

    public function __construct()
    {
        $host = '';
        $port = '';
        $timeout = 0;
        $db = 0;
        $pass = '';
        if ($host) $this->host = $host;
        if ($port) $this->port = $port;
        if ($timeout) $this->timeout = $timeout;
        if ($db) $this->db = $db;
        if ($pass) $this->pass = $pass;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'timeout' => $this->timeout,
            'password' => $this->pass,
            'database' => $this->db
        ];
    }

    /**
     * @return mixed
     */
    public function connect()
    {
        if (!$this->redis) $this->redis = new Redis($this->getConfig());
        return $this->redis->connect($this->host, $this->port);
    }

    /**
     * @return bool|mixed
     */
    public function close()
    {
        if ($this->redis)
            return $this->redis->close();
        return true;
    }

    /**
     * 执行行内命令
     * @param array ...$params
     * @return mixed
     */
    public function requestCmd(...$params)
    {
        if (is_array($params[0])) {
            $params = array_values($params);
        }
        return $this->redis->request($params);
    }
}