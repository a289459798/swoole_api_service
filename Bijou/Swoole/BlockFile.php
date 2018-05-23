<?php

namespace Bijou\Swoole;
class BlockFile
{
    /**
     * 读取文件
     * @param $file
     * @param $context
     * @param $offset
     * @param $maxLen
     * @return mixed
     */
    public static function readFile($path, $context, $offset = 0, $maxLen = null)
    {
        return \file_get_contents($path, false, $context, $offset, $maxLen);
    }

    /**
     * 读取内容
     * @param $fp
     * @param int $size
     * @return mixed
     */
    public static function read($fp, $size = 8192)
    {
        return \fread($fp, $size);
    }

    /**
     * 写入文件
     * @param string $path
     * @param string $content
     * @param int $flag
     * @param resource $context
     * @return mixed
     */
    public static function writeFile($path, $content, $flag, $context)
    {
        return \file_put_contents($path, $content, $flag, $context);
    }

    /**
     * 写入数据
     * @param $fp
     * @param $content
     * @param int $length
     * @return mixed
     */
    public static function write($fp, $content, $length = null)
    {
        return \fwrite($fp, $content, $length);
    }

    /**
     * 获取一行
     * @param $fp
     * @param int $length
     * @return mixed
     */
    public static function gets($fp, $length = null)
    {
        return \fgets($fp, $length);
    }

    /**
     * 打开文件
     * @param $file
     * @param $mode
     * @return mixed
     */
    public static function open($file, $mode = "w")
    {
        return \fopen($file, $mode);
    }

    /**
     * 关闭文件
     * @param $fp
     * @return bool
     */
    public static function close($fp)
    {
        return \fclose($fp);
    }
}
