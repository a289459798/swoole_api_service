<?php

/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/15
 * Time: 16:30
 */

namespace Bijou;

use Bijou\Decorator\Decorator;
use Bijou\Decorator\ExceptionDecorator;
use Bijou\Decorator\RunTimeDecorator;
use Bijou\Decorator\TimeDecorator;
use Bijou\Exception\MethodNotAllowException;
use Bijou\Exception\NoFoundException;
use Bijou\Exception\PHPException;
use Bijou\Http\Frame;
use Swoole\Http;
use Swoole\WebSocket;
use Swoole\Http\Request;
use Swoole\Http\Response;

class App
{
    private $server;
    private $route;
    private $runTimeDecorator;
    private $exceptionDecorator;
    private $requests;

    /**
     * 设置监听的ip与端口
     *
     * App constructor.
     * @param array $ips
     */
    public function __construct($ips, $openWebSocket = false)
    {

        $this->requests = [];
        $this->route = new Route();
        $mode = $ips[2] ?? SWOOLE_PROCESS;
        $flag = $ips[3] ?? SWOOLE_SOCK_TCP;

        if ($openWebSocket) {

            $this->server = new WebSocket\Server($ips[0], $ips[1], $mode, $flag);
        } else {
            $this->server = new Http\Server($ips[0], $ips[1], $mode, $flag);
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
     * @param array $ips
     * @param array $config
     */
    public function addListener(Array $ips, Array $config = null)
    {
        $port = $this->server->addlistener($ips[0], $ips[1], $ips[2]);

        isset($config) && $port->set($config);
    }

    /**
     * @param $className
     */
    public function setWebSocket($className)
    {
        if ($this->server instanceof WebSocket\Server) {

            if (is_callable([$className, "onOpen"])) {
                $this->server->on('open', function (WebSocket\Server $server, Http\Request $request) use($className) {
                    call_user_func_array([$className, "onOpen"], [new \Bijou\Http\WebSocket($server), new \Bijou\Http\Request($request)]);
                });
            }

            $this->server->on('message', function (WebSocket\Server $server, WebSocket\Frame $frame) use($className) {
                call_user_func_array([$className, "onMessage"], [new \Bijou\Http\WebSocket($server), new Frame($frame)]);
            });

            is_callable([$className, "onClose"]) && $this->server->on('close', [$className, "onClose"]);
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

    /**
     * 添加装饰者
     * @param Decorator $decorator
     */
    public function addDecorator(Decorator $decorator)
    {

        if ($decorator instanceof RunTimeDecorator) {
            $this->runTimeDecorator = $decorator;
        } else if ($decorator instanceof ExceptionDecorator) {
            $this->exceptionDecorator = $decorator;
        }
    }

    public function onRequest(Request $request, Response $response)
    {

        try {
            $this->route->dispatch($request, $response, $this);
        } catch (\Exception $e) {
            $this->handlerException($e, $request, $response);
        } catch (\Throwable $e) {
            $this->handlerException($e, $request, $response);
        }
    }

    /**
     * @param Request $request
     */
    public function requestStart(Request $request)
    {
        if (isset($this->runTimeDecorator)) {

            $this->requests[$request->fd] = $this->runTimeDecorator->getCurrentTime();
        }
    }

    /**
     * @param Request $request
     */
    public function requestEnd(Request $request)
    {
        if (isset($this->runTimeDecorator)) {
            if (isset($this->requests[$request->fd])) {
                $this->runTimeDecorator->setApi($request->server['path_info']);
                $endTime = $this->runTimeDecorator->getCurrentTime();
                $this->runTimeDecorator->setRunTime($endTime - $this->requests[$request->fd]);
                unset($this->requests[$request->fd]);
            }
        }
    }

    /**
     * @param \Throwable $throwable
     * @param Request $request
     */
    public function requestError(\Throwable $throwable, Request $request)
    {
        if (isset($this->exceptionDecorator)) {
            $this->exceptionDecorator->setApi($request->server['path_info']);
            $this->exceptionDecorator->throwException($throwable);
        }

        $this->requestEnd($request);
    }


    private function handlerException($e, $request, $response)
    {

        if ($e instanceof NoFoundException) {

        } else if ($e instanceof MethodNotAllowException) {

        } else if ($e instanceof PHPException) {

        } else {

            $this->requestError($e, $request);
            $e = new PHPException($request, $response);
        }

        $e->throwException($e);
    }


    public function run()
    {
        $this->server->start();
    }

}