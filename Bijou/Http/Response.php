<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/18
 * Time: 15:34
 */

namespace Bijou\Http;

use Bijou\Decorator\ResponseDecorator;
use Swoole\Http;

class Response
{
    private $response;
    private $responseDecorator;

    public function __construct(Http\Response $response, ResponseDecorator $responseDecorator = null)
    {
        $this->response = $response;
        $this->responseDecorator = $responseDecorator;
    }

    /**
     * 结束Http响应，发送json数据
     * @param array|string $data
     */
    public function send($data)
    {
        if ($this->responseDecorator) {
            $this->response->end($this->responseDecorator->format($data));
        } else {
            if (is_array($data)) {
                $data = json_encode($data);
            }
            $this->response->end($data);
        }
    }

    /**
     * 结束http响应，发送文本数据
     * @param $data
     */
    public function sendText($data)
    {
        $this->response->end($data);
    }

    /**
     * 启用Http-Chunk分段向浏览器发送数据
     * @param $data
     */
    public function setChunkData($data)
    {

        $this->response->write($data);
    }

    /**
     * 设置返回的http状态码
     * @param $staus
     */
    public function setStatus($staus)
    {
        $this->response->status($staus);
    }

    /**
     * 设置返回的cookie
     * @param Cookie $cookie
     */
    public function setCookie(Cookie $cookie)
    {
        $cookies = $cookie->getAll();
        foreach ($cookies as $v) {
            $this->response->cookie($v['name'], $v['value'], $v['expire'], $v['path'], $v['domain'], $v['secure'], $v['httponly']);
        }
    }

    /**
     * 设置返回头信息
     * @param array $headers
     */
    public function setHeaders(Array $headers)
    {
        foreach ($headers as $k => $v) {
            $this->setHeader($k, $v);
        }
    }

    /**
     * 设置返回头信息
     * @param $key
     * @param $value
     */
    public function setHeader($key, $value)
    {
        $this->response->header($key, $value);
    }

    /**
     * 设置gzip压缩等级
     * @param $level
     */
    public function setGZipLevel($level)
    {
        $this->response->gzip($level);
    }

    /**
     * 发送静态文件
     * @param $fileName
     */
    public function sendFile($fileName)
    {
        $this->response->sendfile($fileName);
    }

    public function __get($name)
    {
        if (isset($this->response[$name])) {

            return $this->response[$name];
        }
    }

    public function __call($name, $arguments)
    {
        if (is_callable([$this->response, $name])) {
            call_user_func_array([$this->response, $name], $arguments);
        }
    }

    public function __toString()
    {
        return json_encode($this->response, true);
    }
}