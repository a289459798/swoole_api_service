<?php

namespace Bijou\Swoole;

use Swoole\MsgQueue;

/**
 * @since 2.1.3
 */
class MemMsgQueue
{

    protected $queue;

    /**
     * @param $len [required]
     */
    public function __construct($len)
    {
        $this->queue = new MsgQueue($len);
    }


    /**
     * @param $data [required]
     * @param $type [optional]
     * @return mixed
     */
    public function push($data, $type = null)
    {
        return $this->queue->push($data, $type);
    }

    /**
     * @param $type [optional]
     * @return mixed
     */
    public function pop($type = null)
    {
        return $this->queue->pop();
    }

    /**
     * @param $blocking [required]
     * @return mixed
     */
    public function setBlocking($blocking)
    {
        return $this->queue->setBlocking($blocking);
    }

    /**
     * @return mixed
     */
    public function stats()
    {
        return $this->queue->stats();
    }

    /**
     * @return mixed
     */
    public function destory()
    {
        return $this->queue->destory();
    }


}

