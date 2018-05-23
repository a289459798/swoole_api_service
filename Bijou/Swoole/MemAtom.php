<?php

namespace Bijou\Swoole;

use Swoole\Atomic;

class MemAtom
{
    /**
     * @var Atomic
     */
    public $atomic;

    /**
     * MemAtom constructor.
     * @param int $value
     */
    public function __construct($value = -1)
    {
        $this->atomic = new Atomic($value);
    }

    /**
     * 增加计数
     * @param int $value
     * $add_value要增加的数值，默认为1
     * $add_value必须为正整数
     * $add_value与原值相加如果超过42亿，将会溢出，高位数值会被丢弃
     * add方法操作成功后返回结果数值
     * @return mixed
     */
    public function add($value = 1)
    {
        return $this->atomic->add($value);
    }

    /**
     * 减少计数
     * @param int $value
     * $sub_value要减少的数值，默认为1
     * $sub_value必须为正整数
     * $sub_value与原值相减如果低于0将会溢出，高位数值会被丢弃
     * sub方法操作成功后返回结果数值
     * @return mixed
     */
    public function sub($value = 1)
    {
        return $this->atomic->sub($value);
    }

    /**
     * 获取当前计数的值
     * @param int $value
     * @return mixed
     */
    public function get($value = 1)
    {
        return $this->atomic->get($value);
    }

    /**
     * 将当前值设置为指定的数字。
     * @param int $value
     * @return mixed
     */
    public function set($value = 1)
    {
        return $this->atomic->set($value);
    }

    /**
     * 如果当前数值等于参数1，则将当前数值设置为参数2
     * @param $cmpValue
     * @param $newValue
     * 如果当前数值等于$cmp_value返回true，并将当前数值设置为$set_value
     * 如果不等于返回false
     * $cmp_value，$set_value 必须为小于42亿的整数
     * @return mixed
     */
    public function cmpset($cmpValue, $newValue)
    {
        return $this->atomic->cmpset($cmpValue, $newValue);
    }

    /**
     * 当原子计数的值为0时程序进入等待状态。
     * 另外一个进程调用wakeup可以再次唤醒程序。底层基于Linux Futex实现，使用此特性，可以仅用4字节内存实现一个等待、通知、锁的功能。
     * @param int $timeout
     * $timeout 指定超时时间，默认为-1，表示永不超时，会持续等待直到有其他进程唤醒
     * 超时返回false，错误码为EAGAIN，可使用swoole_errno函数获取
     * 成功返回true，表示有其他进程通过wakeup成功唤醒了当前的锁
     * 使用wait/wakeup特性时，原子计数的值只能为0或1，否则会导致无法正常使用
     * 当然原子计数的值为1时，表示不需要进入等待状态，资源当前就是可用。wait函数会立即返回true
     * @return mixed
     */
    public function wait($timeout = 1)
    {
        return $this->atomic->wait($timeout);
    }

    /**
     * 唤醒处于wait状态的其他进程
     * 当前原子计数如果为0时，表示没有进程正在wait，wakeup会立即返回true
     * 当前原子计数如果为1时，表示当前有进程正在wait，wakeup会唤醒等待的进程，并返回true
     * 如果同时有多个进程处于wait状态，$n参数可以控制唤醒的进程数量
     * 被唤醒的进程返回后，会将原子计数设置为0，这时可以再次调用wakeup唤醒其他正在wait的进程
     * @param int $count
     * @return mixed
     */
    public function wakeup($count = 1)
    {
        return $this->atomic->wakeup($count);
    }
}

