<?php

namespace Bijou\Swoole;
class AsyncUtil
{
    /**
     * 异步查找DNS
     * @param $hostname
     * @param callable $cb
     * @return mixed
     */
    public static function dnsLookup($hostname, callable $cb)
    {
        return \swoole_async_dns_lookup($hostname, $cb);
    }

    /**
     * 禁用DNS缓存
     * @return mixed
     */
    public static function dnsDisableCache()
    {
        return \swoole_async_set(['disable_dns_cache' => true]);
    }

    /**
     * 手动设置DNS服务器
     * @param $server
     * @return mixed
     */
    public static function dnsSetServer($server)
    {
        return \swoole_async_set(['dns_server' => $server]);
    }

    /**
     * 使用随机DNS服务器
     * @return mixed
     */
    public static function dnsRandom()
    {
        return \swoole_async_set(['dns_lookup_random' => true]);
    }

    /**
     * 异步执行本地命令
     * @param $cmd
     * @param callable $cb
     * @return mixed
     */
    public static function exec($cmd, callable $cb)
    {
        return \Swoole\Async::exec($cmd, $cb);
    }

    /**
     * 间隔执行
     * callbackFunction(int $timer_id, mixed $params = null);
     * @param $ms
     * @param $cb
     * @return mixed
     */
    public static function setInterval($ms, $cb)
    {
        return \swoole_timer_tick($ms, $cb);
    }

    /**
     * 延后执行
     * callbackFunction(int $timer_id, mixed $params = null);
     * 基于timerfd+epoll实现的异步毫秒定时器，可完美的运行在EventLoop中
     * @param $ms
     * @param $cb
     * @param $args
     * @return mixed
     */
    public static function setTimeout($ms, $cb, $args = null)
    {
        return \swoole_timer_after($ms, $cb, $args);
    }

    /**
     * 清除定时器
     * @param $timerId
     * @return mixed
     */
    public static function clearTimer($timerId)
    {
        return \swoole_timer_clear($timerId);
    }
}
