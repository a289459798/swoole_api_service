<?php

namespace Bijou\Swoole;

use Swoole\Coroutine as Co;

class CoUtil
{


    /** 创建一个协程
     * @param callable $coFunc
     * @return mixed
     */
    public static function createCo(callable $coFunc)
    {
        return Co::create($coFunc);
    }

    /**
     * 获取当前协程ID
     * @return mixed
     */
    public static function getCoId()
    {
        return Co::getuid();
    }

    /**
     * 挂前协程
     * @param $coId
     * @return mixed
     */
    public static function CoSuspend($coId)
    {
        return Co::suspend($coId);
    }

    /**
     * 恢复某个协程，使其继续运行
     * @param $coId
     * @return mixed
     */
    public static function CoResume($coId)
    {
        return Co::resume($coId);
    }

    /**
     * 查找DNS
     * @param $hostname
     * @return mixed
     */
    public static function dnsLookup($hostname)
    {
        return Co::gethostbyname($hostname);
    }

    /**
     * 查询域名对应的IP地址
     * @param string $domain
     * @param int $family
     * @param int $socktype
     * @param int $protocol
     * @param string|null $service
     * @return mixed
     */
    public static function getAddrInfo(string $domain, int $family = AF_INET, int $socktype = SOCK_STREAM, int $protocol = IPPROTO_TCP, string $service = null)
    {
        return Co::getaddrinfo($domain, $family, $socktype, $protocol, $service);
    }

    /**
     * 执行本地命令
     * @param $cmd
     * @return mixed
     */
    public static function exec($cmd)
    {
        return Co::exec($cmd);
    }

    /**
     * 暂停若干秒
     * @param $seconds
     * @return mixed
     */
    public static function sleep($seconds)
    {
        return Co::sleep($seconds);
    }


    /**
     * @param callable $cb
     * @param array $params
     * @return mixed
     */
    public static function callUserFuncArray(callable $cb, array $params)
    {
        return Co::call_user_func_array($cb, $params);
    }
}
