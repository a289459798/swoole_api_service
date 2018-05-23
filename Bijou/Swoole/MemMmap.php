<?php

namespace Bijou\Swoole;

use Spf\Std\StdFile;

class MemMmap
{
    /**
     * @var resource
     */
    public $handler;

    /**
     * MemMmap constructor.
     * 创建文件内存映射
     * @param string $file 磁盘文件名称，必须是存在的文件，如果文件不存在将会创建失败
     * @param int $size 映射操作，默认为整个文件的长度，操作系统会分配同等大小的内存
     * @param int $offset 文件的映射起始位置，默认为0
     */
    public function __construct($file, $size = -1, $offset = 0)
    {
        $this->handler = \swoole_mmap::open($file, $size, $offset);
    }

    /**
     * 读取内容
     * @param int $size
     * @return mixed
     */
    public function read($size = 8192)
    {
        return BlockFile::read($this->handler, $size);
    }

    /**
     * 写入内容
     * @param $string
     * @param null $length
     * @return mixed
     */
    public function write($string, $length = null)
    {
        return BlockFile::write($this->handler, $string, $length);
    }

    /**
     * 获取最多一行
     * @param null $length
     * @return mixed
     */
    public function gets($length = null)
    {
        return BlockFile::gets($this->handler, $length);
    }

    /**
     * 同步数据，fflush将内存中的数据写入到磁盘
     * @return bool
     */
    public function fflush()
    {
        return \fflush($this->handler);
    }

    /**
     * 关闭文件
     * @return bool
     */
    public function close()
    {
        return BlockFile::close($this->handler);
    }
}
