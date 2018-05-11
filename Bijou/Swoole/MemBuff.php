<?php

namespace Bijou\Swoole;

use Swoole\Buffer;

class MemBuff
{
    /**
     * @var Buffer
     */
    public $buffer;

    public function __construct($size = 128)
    {
        $this->buffer = new Buffer($size);
    }

    /**
     * 将一个字符串数据追加到缓存区末尾。
     * @param string $string
     */
    public function append(string $string)
    {
        $this->buffer->append($string);
    }

    /**
     * 从缓冲区中取出内容
     * @param int $offset 表示偏移量，如果为负数，表示倒数计算偏移量
     * @param int $length 表示读取数据的长度，默认为从 $offset 到整个缓存区末尾
     * @param bool $remove 表示从缓冲区的头部将此数据移除。只有 $offset = 0 时此参数才有效
     */
    public function substr(int $offset, int $length = -1, bool $remove = false)
    {
        $this->buffer->substr($offset, $length, $remove);
    }

    /**
     * 清理缓存区数据。
     */
    public function clear()
    {
        $this->buffer->clear();
    }

    /**
     * 为缓存区扩容。
     * @param int $new_size
     */
    public function expand(int $new_size)
    {
        $this->buffer->expand($new_size);
    }

    /**
     * 为缓存区扩容。
     * @param int $offset
     * @param string $data
     */
    public function write(int $offset, string $data)
    {
        $this->buffer->write($offset, $data);
    }

    /**
     * 读取缓存区任意位置的内存。
     * @param int $offset
     * @param int $length
     */
    public function read(int $offset, int $length)
    {
        $this->buffer->read($offset, $length);
    }

    /**
     * 回收缓冲中已经废弃的内存。
     * 此方法能够在不清空缓冲区和使用 swoole_buffer->clear() 的情况下
     * 回收通过 swoole_buffer->substr() 移除但仍存在的部分内存空间。
     */
    public function recycle()
    {
        $this->buffer->recycle();
    }


}
