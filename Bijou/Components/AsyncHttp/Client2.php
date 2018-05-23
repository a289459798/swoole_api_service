<?php

namespace Spf\Library\Swoole\Common {

    use Swoole\Http\Client;

    /**
     * Class AsyncHttp
     * 支持Http-Chunk、Keep-Alive、form-data
     * Http协议版本为HTTP/1.1
     * gzip压缩格式支持需要依赖zlib库
     * 不依赖其他第三方库
     * @package Spf\Library\Swoole\Common
     */
    class AsyncHttp
    {
        public $ssl = false;
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
                $params['port'] = 443;
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
        }

        /**
         * 连接参数
         * @param callable $cb
         */
        public function prepare(callable $cb)
        {
            if ($this->http) {
                $cb();
            } else {
                $this->http = new Client($this->host, $this->port, $this->ssl);
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
                $cb();
            }
        }

        /**
         * GET请求
         * @param string $url
         * @param callable $cb
         * @param array $cookies
         * @param array $headers
         */
        public function get($url, callable $cb, array $cookies = [], array $headers = [])
        {
            if (!$url) {
                $url = $this->path;
                if ($this->query) $url .= "?" . $this->query;
            }
            $this->prepare(function () use ($url, $cb, $headers, $cookies) {
                if ($headers) $this->http->setHeaders($headers);
                if ($cookies) $this->http->setCookies($cookies);
                $this->http->get($url, function (Client $client) use ($cb) {
                    $cb($client);
                });
            });
        }

        /**
         * POST请求
         * @param string $url
         * @param callable $cb
         * @param array|string $data
         * @param array $cookies
         * @param array $headers
         */
        public function post($url, callable $cb, $data = [], array $cookies = [], array $headers = [])
        {
            if (!$url) {
                $url = $this->path;
                if ($this->query) $url .= "?" . $this->query;
            }
            $this->prepare(function () use ($url, $cb, $data, $headers, $cookies) {
                if ($headers) $this->http->setHeaders($headers);
                if ($cookies) $this->http->setCookies($cookies);
                $this->http->post($url, $data, function (Client $client) use ($cb) {
                    $cb($client);
                });
            });
        }


        /**
         * 任意http请求
         * @param string $url
         * @param callable $cb
         * @param string $method
         * @param array $data
         * @param array $files
         * @param array $cookies
         * @param array $headers
         */
        public function execute($url, callable $cb, $method = "post", array $data = [], array $files = [], array $cookies = [], array $headers = [])
        {
            if (!$url) {
                $url = $this->path;
                if ($this->query) $url .= "?" . $this->query;
            }
            $this->prepare(function () use ($url, $cb, $method, $data, $files, $headers, $cookies) {
                if ($files) {
                    foreach ($files as $file) {
                        $this->http->addFile($file, basename($file));
                    }
                }
                if ($headers) $this->http->setHeaders($headers);
                if ($cookies) $this->http->setCookies($cookies);
                $this->http->setMethod($method);
                $this->http->execute($url, function (Client $client) use ($cb) {
                    $cb($client);
                });
            });
        }


        /**
         * 下载文件(get)
         * @param $url
         * @param callable $cb
         * @param $localPath
         * @param int $offset
         * @param array $cookies
         * @param array $headers
         */
        public function download($url, callable $cb, $localPath, $offset = 0, array $cookies, array $headers)
        {
            if (!$url) {
                $url = $this->path;
                if ($this->query) $url .= "?" . $this->query;
            }
            $this->prepare(function () use ($url, $cb, $localPath, $offset, $headers, $cookies) {
                if ($headers) $this->http->setHeaders($headers);
                if ($cookies) $this->http->setCookies($cookies);
                $this->http->download($url, $localPath, function (Client $client) use ($cb) {
                    $cb($client);
                }, $offset);
            });
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
    }
}