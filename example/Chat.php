<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 00:28
 */

namespace Bijou\Example;


use Bijou\Http\Frame;
use Bijou\Http\Request;
use Bijou\Http\WebSocket;

class Chat
{
    public function onOpen(WebSocket $server, Request $request)
    {
        echo 'onOpen: 连接标识：' . $request->getClient();
        $server->send($request->getClient(), json_encode([1, 2, 3, 4]));
    }

    public function onMessage(WebSocket $server, Frame $frame)
    {
        echo 'onMessage:' . $frame->getData();
        $server->send($frame->getClient(), json_encode([1, 2, 3, 4]));
    }

    public function onClose()
    {
        echo 'onClose';
    }
}