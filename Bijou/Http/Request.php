<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/18
 * Time: 15:34
 */

namespace Bijou\Http;

use Swoole\Http;

class Request
{
    private $request;
    private $header;

    public function __construct(Http\Request $request)
    {
        $this->request = $request;
    }

    /**
     * 获取请求的客户端标识
     * @return mixed
     */
    public function getClient()
    {
        return $this->request->fd;
    }

    public function getMethod()
    {
        return $this->request->server['request_method'];
    }

    /**
     * @return Header
     */
    public function getHeader()
    {

        if (!$this->header) {
            $this->header = new Header($this->request->header);
        }

        return $this->header;
    }

    /**
     * 获取用户ip
     * @return mixed
     */
    public function getIp()
    {
        return $this->request->server['remote_addr'];
    }

    /**
     * 获取响应服务器端口
     * @return mixed
     */
    public function getPort()
    {
        return $this->request->server['server_port'];
    }

    public function __get($name)
    {
        if (isset($this->request[$name])) {

            return $this->request[$name];
        }
    }

    public function __toString()
    {
        return json_encode($this->request, true);
    }
}