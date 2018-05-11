<?php

namespace Bijou\Swoole;

use Swoole\Channel;

class MemChan
{
    /**
     * @var Channel
     */
    public $chan;

    public function __construct(int $size = 64)
    {
        $this->chan = new Channel($size);
    }

    /**
     * 向通道写入数据
     * $data可以为任意PHP变量，当$data是非字符串类型时底层会自动进行串化
     * $data的尺寸超过8K时会启用临时文件存储数据
     * $data必须为非空变量，如空字符串、空数组、0、null、false
     * 写入成功返回true
     * 通道的空间不足时写入失败并返回false
     * @param $data
     * @return mixed|Channel
     */
    public function push($data)
    {
        return $this->chan->push($data);
    }

    /**
     * 弹出数据
     * 当通道内有数据时自动将数据弹出并还原为PHP变量
     * 当通道内没有任何数据时pop会失败并返回false
     * @return mixed
     */
    public function pop()
    {
        return $this->chan->pop();
    }

    /**
     * 返回一个数组，包括2项信息
     * queue_num 通道中的元素数量
     * queue_bytes 通道当前占用的内存字节数
     */
    public function stats()
    {
        return $this->chan->stats();
    }
}
