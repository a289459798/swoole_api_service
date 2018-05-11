<?php

namespace Bijou\Components\CoHttp;

use Swoole\Coroutine\Http\Client as SwooleClient;

/**
 * Class AsyncHttp
 * 需要在编译swoole时增加--enable-coroutine来开启此功能
 * 支持Http-Chunk、Keep-Alive特性，暂不支持form-data格式
 * Http协议版本为HTTP/1.1
 * gzip压缩格式支持需要依赖zlib库
 * 不依赖其他第三方库
 * @package Spf\Library\Swoole\Common
 */
class Client
{
    public $ssl = false;
    public $ip;
    public $host = 'localhost';
    public $port = 80;
    public $user = '';
    public $pass = '';
    public $path = '';
    public $query = '';
    public $fragment = '';
    public $method = '';
    public $timeout = 0;
    public $keepAlive = true;
    public $proxy = [];
    public $lastPath = '';
    public $lastMethod = '';
    public $lastData = '';
    /**
     * @var \Swoole\Http\Client
     */
    public $http;

    /**
     * AsyncHttp constructor.
     * @param $url
     * @param bool $ssl
     * @param int $timeout
     * @param bool $keepAlive
     * @param array $proxy
     */
    public function __construct($url, $ssl = false, $timeout = 0, $keepAlive = false, $proxy = [])
    {
        $this->timeout = $timeout;
        $this->keepAlive = $keepAlive;
        $this->proxy = $proxy;
        $params = parse_url($url);
        if (isset($params['scheme']) && $params['scheme'] === 'https') {
            $this->ssl = true;
        } else {
            $this->ssl = $ssl;
        }
        if (isset($params['host'])) $this->host = $params['host'];
        if (isset($params['port'])) $this->port = $params['port'];
        if (isset($params['user'])) $this->user = $params['user'];
        if (isset($params['pass'])) $this->pass = $params['pass'];
        if (isset($params['path'])) $this->path = $params['path'];
        if (isset($params['query'])) $this->query = $params['query'];
        if (isset($params['fragment'])) $this->fragment = $params['fragment'];

        $this->http = new SwooleClient($this->host, $this->port, $this->ssl);
        $config = [
            'timeout' => $this->timeout,
            'keep_alive' => $this->keepAlive,
        ];
        $header = ['Host' => $this->host];
        if (isset($this->proxy['host'])) $config['http_proxy_host'] = $this->proxy['host'];
        if (isset($this->proxy['port'])) $config['http_proxy_port'] = $this->proxy['port'];
        if ($this->user || $this->pass) $header['Authorization'] = sprintf("%s %s", "Basic", base64_encode("{$this->user}:{$this->pass}"));

        $this->http->setHeaders($header);
        $this->http->set($config);
    }

    /**
     * @param $url
     * @param array $cookies
     * @param array $headers
     * @return mixed
     */
    public function get($url, array $cookies = [], array $headers = [])
    {
        if (!$url) {
            $url = $this->path;
            if ($this->query) $url .= "?" . $this->query;
        }
        if ($headers) $this->http->setHeaders($headers);
        if ($cookies) $this->http->setCookies($cookies);
        $this->lastPath = $url;
        $this->lastMethod = 'GET';
        $this->lastData = '';
        return $this->http->get($url);
    }

    /**
     * POST请求
     * @param $url
     * @param array $data
     * @param array $cookies
     * @param array $headers
     * @return mixed
     */
    public function post($url, $data = [], array $cookies = [], array $headers = [])
    {
        if (!$url) {
            $url = $this->path;
            if ($this->query) $url .= "?" . $this->query;
        }
        if ($headers) $this->http->setHeaders($headers);
        if ($cookies) $this->http->setCookies($cookies);
        $this->lastPath = $url;
        $this->lastMethod = 'POST';
        $this->lastData = $data;
        return $this->http->post($url, $data);
    }

    /**
     * 任意http请求
     * @param $url
     * @param string $method
     * @param array|string $data
     * @param array $files
     * @param array $cookies
     * @param array $headers
     * @return mixed
     */
    public function execute($url, $method = "POST", $data = [], array $files = [], array $cookies = [], array $headers = [])
    {
        if (!$url) {
            $url = $this->path;
            if ($this->query) $url .= "?" . $this->query;
        }
        if ($data) {
            $this->http->setData($data);
            $this->lastData = $data;
        }
        if ($files) {
            foreach ($files as $file) {
                $this->http->addFile($file, basename($file));
            }
        }
        if ($headers) $this->http->setHeaders($headers);
        if ($cookies) $this->http->setCookies($cookies);
        $this->http->setMethod($method);
        $this->lastPath = $url;
        $this->lastMethod = $method;
        return $this->http->execute($url);
    }

    /**
     *
     */
    public function close()
    {
        if ($this->http) {
            $this->http->close();
        }
    }

    /**
     * 返回http状态码
     * -1 连接超时，服务器未监听端口或网络丢失，可以读取$errCode获取具体的网络错误码
     * -2 请求超时，服务器未在规定的timeout时间内返回response
     * -3 客户端请求发出后，服务器强制切断连接
     * 其他情况为正常的HTTP状态码
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->http->statusCode;
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return [
            'status' => $this->http->statusCode,
            'header' => $this->http->headers,
            'body' => $this->http->body
        ];
    }

    public function getLastRequest()
    {
        return [
            'path' => $this->lastPath,
            'method' => $this->lastMethod,
            'header' => $this->http->requestHeaders,
            'body' => $this->lastData
        ];
    }

}