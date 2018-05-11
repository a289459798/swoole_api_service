<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/18
 * Time: 15:34
 */

namespace Bijou\Http;

use Swoole\WebSocket;

/**
 * Class Frame
 * @package Bijou\Http
 *
 * @property int $fd
 * @property bool $finish
 * @property string $opcode
 * @property string $data
 */
class Frame
{
    private $frame;

    public function __construct(WebSocket\Frame $frame)
    {
        $this->frame = $frame;
    }

    /**
     * 客户端的标识
     * @return int
     */
    public function getClient()
    {
        return $this->frame->fd;
    }

    /**
     * 接受的数据
     * 文本内容也可以是二进制数据，可以通过getDataType的值来判断
     * @return string
     */
    public function getData()
    {
        return $this->frame->data;
    }


    public function getDataType()
    {
        return $this->frame->opcode;
    }

    public function __get($name)
    {
        return $this->frame[$name];
    }
}