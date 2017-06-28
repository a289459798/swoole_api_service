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
    private $config = [];

    public static function create()
    {
        return new static();
    }

    public function setIp($ip) {
        $this->config['ip'] = $ip;
        return $this;
    }

    public function setPort($port) {
        $this->config['port'] = $port;
        return $this;
    }

    public function keepAlive() {
        $this->config['keepAlive'] = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function build()
    {
        $this->headers = [];
        $this->cookies = [];

        if (isset($this->config['ip'])) {
            $this->ip = $this->config['ip'];
            $port = isset($this->config['port']) ? $this->config['port'] : 80;
            $ssl = isset($this->config['ssl']) ? $this->config['ssl'] : false;
            $this->client = new \Swoole\Coroutine\Http\Client($this->ip, $port, $ssl);
        }

        return $this;
    }

    /**
     * get请求
     * @param $url
     */
    public function get($url)
    {
        $this->method = 'GET';
        $this->data = null;
        return $this->execute($url);
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
        return $this->execute($url);
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
        return $this->execute($url);
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

        if(isset($this->url['host'])) {
            $ip = $this->url['host'];

            if ($ip != $this->ip || !$this->client || !$this->client->isConnected()) {
                $ssl = $this->url['scheme'] == 'https' ? true : false;
                $config = [
                    "ip" => $ip,
                    "port" => $this->url['port'] ? $this->url['port'] : ($ssl ? 443 : 80),
                    "ssl" => $ssl
                ];

                if ($this->config) {
                    $config = array_merge($this->config, $config);
                }

                $this->build($config);
            }
        }

        $this->client->setHeaders($this->headers);
        $this->client->setMethod($this->method);
        $this->client->setCookies($this->cookies);
        $this->data && $this->client->setData($this->data);
        $this->client->execute($this->url['path'] . (isset($this->url['query']) ? "?" . $this->url['query'] : ""));
        $body = $this->client->body;
        if (!$this->config['keepAlive'] && $this->client->isConnected()) {
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