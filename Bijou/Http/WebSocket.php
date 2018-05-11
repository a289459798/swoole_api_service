<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/18
 * Time: 15:34
 */

namespace Bijou\Http;

const WEBSOCKET_TEXT = WEBSOCKET_OPCODE_TEXT;
const WEBSOCKET_BINARY = WEBSOCKET_OPCODE_BINARY;

/**
 * Class WebSocket
 * @package Bijou\Http
 *
 * @method push($fd, $data, $binary_data = WEBSOCKET_OPCODE_TEXT, $finish = true)
 * @method pack($data, $opcode = WEBSOCKET_OPCODE_TEXT, $finish = true, $mask = false)
 */
class WebSocket
{

    private $server;

    public function __construct(\Swoole\WebSocket\Server $server)
    {
        $this->server = $server;
    }

    /**
     * 向某个WebSocket客户端连接推送数据
     * @param      $fd
     * @param      $data
     * @param int $binary_data
     * @param bool $finish
     * @return bool
     */
    public function send($fd, $data, $binary_data = WEBSOCKET_TEXT, $finish = true)
    {

        $this->server->push($fd, $data, $binary_data, $finish);
    }

    public function __call($name, $arguments)
    {
        if (is_callable([$this->server, $name])) {
            call_user_func_array([$this->server, $name], $arguments);
        }
    }
}