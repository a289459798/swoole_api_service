<?php

namespace Bijou\Swoole;

use Swoole\Lock;

class MemLock
{
    public $memLock;

    /**
     * MemLock constructor.
     * @param $lockType
     * 读写锁 \SWOOLE_RWLOCK
     * 文件锁 \SWOOLE_FILELOCK 2
     * 读写锁 \SWOOLE_RWLOCK
     * 信号量 \SWOOLE_SEM 4
     * 互斥锁 \SWOOLE_MUTEX 3
     * 自旋锁 \SWOOLE_SPINLOCK 5
     * @param $lockFile
     */
    public function __construct($lockType, $lockFile = null)
    {
        $this->memLock = new Lock($lockType);
    }

    /**
     * 加锁操作。如果有其他进程持有锁，那这里将进入阻塞，直到持有锁的进程unlock。
     * @return mixed
     */
    public function lock()
    {
        return $this->memLock->lock();
    }

    /**
     * 加锁操作。与lock方法不同的是，trylock()不会阻塞，它会立即返回。
     * 加锁成功返回true，此时可以修改共享变量。
     * 加锁失败返回false，表示有其他进程持有锁。
     * @return mixed
     */
    public function tryLock()
    {
        return $this->memLock->trylock();
    }

    /**
     * 释放锁
     * @return mixed
     */
    public function unlock()
    {
        return $this->memLock->unlock();
    }

    /**
     * 只读加锁。lock_read方法表示仅锁定读，只有SWOOLE_RWLOCK和SWOOLE_FILELOCK类型的锁支持只读加锁
     * 在持有读锁的过程中，其他进程依然可以获得读锁，可以继续发生读操作
     * 在独占锁加锁时，其他进程无法再进行任何加锁操作，包括读锁
     * 当另外一个进程获得了独占锁(调用$lock->lock/$lock->trylock)时，$lock->lock_read()会发生阻塞，直到持有独占锁的进程释放锁
     * @return mixed
     */
    public function lockRead()
    {
        return $this->memLock->lock_read();
    }

    /**
     * 加锁。此方法与lock_read相同，但是非阻塞的。
     * @return mixed
     */
    public function tryLockRead()
    {
        return $this->memLock->trylock_read();
    }

    /**
     * 加锁操作，作用于swoole_lock->lock一致，但lockwait可以设置超时时间
     * 在规定的时间内未获得锁，返回false
     * 加锁成功返回true
     * @param float $timeout
     * @return mixed
     */
    public function lockWait($timeout = 1.0)
    {
        return $this->memLock->lockwait($timeout);
    }
}
