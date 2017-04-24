<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/24
 * Time: 13:23
 */

namespace Bijou\Http;


class CoClient
{
    private $headers;
    private $client;
    private $url;
    private $ip;
    private $method;
    private $cookies;
    private $keepAlive;
    private $data;

    public function __construct($keepAlive = false)
    {
        $this->headers = [];
        $this->cookies = [];
        $this->keepAlive = $keepAlive;
    }

    /**
     * get请求
     * @param $url
     */
    public function get($url)
    {
        $this->method = 'GET';
        $this->data = null;
        $this->execute($url);
    }

    /**
     * post 请求
     * @param $url
     * @param $data
     */
    public function post($url, $data)
    {
        $this->method = 'POST';
        $this->data = $data;
        $this->execute($url);
    }

    /**
     * put 请求
     * @param $url
     * @param $data
     */
    public function put($url, $data)
    {
        $this->method = 'PUT';
        $this->data = $data;
        $this->execute($url);
    }

    /**
     * delete 请求
     * @param $url
     */
    public function delete($url)
    {
        $this->method = 'DELETE';
        $this->data = null;
        $this->execute($url);
    }

    /**
     * 设置请求头
     * @param array $headers
     */
    public function setHeaders(Array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * 设置请求组cookie
     * @param array $cookies
     */
    public function setCookies(Array $cookies)
    {
        $this->cookies = $cookies;
    }

    private function execute($url)
    {

        $this->url = parse_url($url);

        $ip = $this->url['host'];

        if ($ip != $this->ip || !$this->client || !$this->client->isConnected()) {
            $this->ip = $ip;
            $ssl = $this->url['scheme'] == 'https' ? true : false;
            $port = $this->url['port'] ? $this->url['port'] : ($ssl ? 443 : 80);
            $this->client = new \Swoole\Coroutine\Http\Client($ip, $port, $ssl);
        }

        $this->client->setHeaders($this->headers);
        $this->client->setMethod($this->method);
        $this->client->setCookies($this->cookies);
        $this->data && $this->client->setData($this->data);
        $this->client->execute($this->url['path'] . (isset($this->url['query']) ? $this->url['query'] : ""));
        $body = $this->client->body;
        if (!$this->keepAlive && $this->client->isConnected()) {
            $this->client->close();
        }
        return $body;
    }

    /**
     * 关闭连接，keepAlive 需要手动调用
     */
    public function close()
    {
        if ($this->client && $this->client->isConnected()) {
            $this->client->close();
        }
    }
}