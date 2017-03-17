<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 00:28
 */

namespace Bijou\Example;


use Swoole\WebSocket\Server;
use Swoole\WebSocket\Frame;

class Chat
{
    public function onOpen()
    {
        echo 'onOpen';
    }

    public function onMessage(Server $server, Frame $frame)
    {
        echo 'onMessage';
        $server->push($frame->fd, json_encode([1, 2, 3, 4]));
    }

    public function onClose()
    {
        echo 'onClose';
    }
}