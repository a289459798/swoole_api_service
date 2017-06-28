<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/24
 * Time: 13:23
 */

namespace Bijou\Http;


class Client
{
    private $headers;
    private $client;
    private $url;
    private $ip;
    private $callback;
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
     * @param callable $callback
     */
    public function get($url, callable $callback)
    {
        $this->method = 'GET';
        $this->data = null;
        $this->parseUrl($url, $callback);
    }

    /**
     * post 请求
     * @param $url
     * @param $data
     * @param callable $callback
     */
    public function post($url, $data, callable $callback)
    {
        $this->method = 'POST';
        $this->data = $data;
        $this->parseUrl($url, $callback);
    }

    /**
     * put 请求
     * @param $url
     * @param $data
     * @param callable $callback
     */
    public function put($url, $data, callable $callback)
    {
        $this->method = 'PUT';
        $this->data = $data;
        $this->parseUrl($url, $callback);
    }

    /**
     * delete 请求
     * @param $url
     * @param callable $callback
     */
    public function delete($url, callable $callback)
    {
        $this->method = 'DELETE';
        $this->data = null;
        $this->parseUrl($url, $callback);
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

    private function parseUrl($url, callable $callback)
    {
        $this->url = parse_url($url);
        $this->callback = $callback;
        \Swoole\Async::dnsLookup($this->url['host'], [$this, 'execute']);
    }

    public function execute($host, $ip)
    {

        if ($ip) {
            if ($ip != $this->ip || !$this->client || !$this->client->isConnected()) {
                $this->ip = $ip;
                $ssl = $this->url['scheme'] == 'https' ? true : false;
                $port = $this->url['port'] ? $this->url['port'] : ($ssl ? 443 : 80);
                $this->client = new \Swoole\Http\Client($ip, $port, $ssl);
            }

            $this->headers += [
                'Host' => $host
            ];
            $this->client->setHeaders($this->headers);
            $this->client->setMethod($this->method);
            $this->client->setCookies($this->cookies);
            $this->data && $this->client->setData($this->data);
            $callback = $this->callback;
            $this->client->execute($this->url['path'] . (isset($this->url['query']) ? "?" . $this->url['query'] : ""), function ($response) use ($callback) {
                call_user_func($callback, $response->body);
            });

            if (!$this->keepAlive && $this->client->isConnected()) {
                $this->client->close();
            }
            return;
        }
        call_user_func($this->callback, false);
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