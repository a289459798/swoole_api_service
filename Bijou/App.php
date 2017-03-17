<?php

/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/15
 * Time: 16:30
 */

namespace Bijou;

use Bijou\Exception\MethodNotAllowException;
use Bijou\Exception\NoFoundException;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

class App
{
    private $server;
    private $route;

    /**
     * 设置监听的ip与端口
     *
     * App constructor.
     * @param array $ips
     */
    public function __construct(Array $ips)
    {

        $this->route = new Route();
        foreach ($ips as $k => $ip) {
            if ($k == 0) {
                $this->server = new Server($ip[0], $ip[1]);
            } else {
                $this->server->addlistener($ip[0], $ip[1], $ip[2]);
            }
        }
        $this->server->on("request", [$this, 'onRequest']);
    }

    /**
     * 加载配置信息
     *
     * @param array $config
     */
    public function loadConfig(Array $config)
    {
        if (isset($config['server'])) {

            $this->server->set($config['server']);
        }
    }

    /**
     * 加载路由
     *
     * @param array $route
     */
    public function loadRoute(Array $route)
    {

        $this->route->loadRoute($route);

    }

    public function onRequest(Request $request, Response $response)
    {

        try {
            $this->route->dispatch($request, $response);
        } catch (\Exception $e) {

            $this->handlerException($e);
        } catch (\Throwable $e) {
            var_dump($e);
        }
    }

    private function handlerException($e)
    {

        if ($e instanceof NoFoundException) {

            $e->throwException();
        } else if($e instanceof MethodNotAllowException) {
            $e->throwException();
        }
        throw $e;
    }


    public function run()
    {
        $this->server->start();
    }

}