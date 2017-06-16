<?php

/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/15
 * Time: 16:30
 */

namespace Bijou;

use Bijou\Manager\CacheManager;
use Bijou\Manager\PoolManager;
use Bijou\Manager\ServiceManager;
use Bijou\Manager\TaskManager;
use Bijou\Decorator\Decorator;
use Bijou\Decorator\ExceptionDecorator;
use Bijou\Decorator\RunTimeDecorator;
use Bijou\Exception\MethodNotAllowException;
use Bijou\Exception\NoFoundException;
use Bijou\Exception\PHPException;
use Bijou\Http\Frame;
use Bijou\Http\Request;
use Bijou\Http\Response;
use Bijou\Interfaces\AsyncTaskInterface;
use Bijou\Interfaces\PoolInterface;
use Bijou\Interfaces\ServiceInterface;
use Swoole\Http;
use Swoole\Process;
use Swoole\WebSocket;

class App
{

    private $server;
    private $route;
    private $runTimeDecorator;
    private $exceptionDecorator;
    private $taskManager;
    private $serviceManager;
    private $process;
    private $cacheManger;
    private $config;

    /**
     * 设置监听的ip与端口
     *
     * App constructor.
     * @param array $ips
     */
    public function __construct($ips, $openWebSocket = false)
    {

        $this->process = [];
        $this->route = new Route();
        $mode = isset($ips[2]) ? $ips[2] : SWOOLE_PROCESS;
        $flag = isset($ips[3]) ? $ips[3] : SWOOLE_SOCK_TCP;

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
        $this->config = $config;
        if (isset($config['server'])) {

            $this->server->set($config['server']);

            if ($config['server']['task_worker_num'] > 0) {
                $this->taskManager = new TaskManager();
                $this->server->on("Task", [$this->taskManager, 'onTask']);
                $this->server->on("Finish", [$this->taskManager, 'onFinish']);
            }
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
                $this->server->on('open', function (WebSocket\Server $server, Http\Request $request) use ($className) {
                    call_user_func_array([$className, "onOpen"], [new \Bijou\Http\WebSocket($server), new \Bijou\Http\Request($request)]);
                });
            }

            $this->server->on('message', function (WebSocket\Server $server, WebSocket\Frame $frame) use ($className) {
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

    public function getRoutes()
    {
        return $this->route->getRoutes();
    }

    public function setCache($cacheDir, $expire = 3600, $mode = BIJOU_CACHE_FILE)
    {
        $this->cacheManger = new CacheManager($cacheDir, $expire, $mode);
    }

    public function getCacheManager()
    {
        return $this->cacheManger;
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

    public function onRequest(Http\Request $request, Http\Response $response)
    {

        $bijouRequest = new Request($request);
        $bijouResponse = new Response($response, $this->runTimeDecorator);
        try {
            $this->route->dispatch($bijouRequest, $bijouResponse, $this);
        } catch (\Exception $e) {
            $this->handlerException($e, $bijouRequest, $bijouResponse);
        } catch (\Throwable $e) {
            $this->handlerException($e, $bijouRequest, $bijouResponse);
        }
    }

    /**
     * 添加一个异步任务并执行
     * @param AsyncTaskInterface $asyncTask
     */
    public function addAsyncTask(AsyncTaskInterface $asyncTask)
    {
        if ($this->taskManager) {
            $this->taskManager->addTask($this->server, $asyncTask);
        }
    }

    /**
     * @param $classPath
     * @param $action
     * @param array $data
     */
    public function startService($classPath, $action, Array $data)
    {
        if (isset($this->process[$classPath])) {
            $this->process[$classPath]->write(json_encode(['service' => $classPath, 'action' => $action, 'data' => $data]));
        }
    }

    /**
     * 注册一个永远在后台执行的service
     * @param ServiceInterface $service
     */
    public function addService(ServiceInterface $service)
    {
        if (!$this->serviceManager) {
            $this->serviceManager = new ServiceManager();
        }
        $this->serviceManager->addService($service);
        $this->process[get_class($service)] = new Process([$this->serviceManager, 'onCommand']);
        $this->server->addProcess($this->process[get_class($service)]);
    }

    /**
     * 添加连接池
     * @param $name
     * @param PoolInterface $driver
     */
    public function addPool($name, PoolInterface $driver)
    {
        if (!isset($this->server->poolManager)) {
            $this->server->poolManager = new PoolManager();
        }

        $this->server->poolManager->addDriver($name, $driver);
    }

    /**
     * 调用连接池连接
     * @param $path
     */
    public function pool($path)
    {
        return $this->server->poolManager->driver($path);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function requestStart(Request $request)
    {
        if (isset($this->runTimeDecorator)) {
            return $this->runTimeDecorator->requestStart($request);
        }
        return true;
    }

    /**
     * @param Request $request
     * @param $data
     */
    public function requestEnd(Request $request, $data = null)
    {

        if (isset($this->runTimeDecorator)) {
            $this->runTimeDecorator->requestEnd($request, $data);
        }
    }

    /**
     * @param \Throwable $throwable
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public function requestError(\Throwable $throwable, Request $request, Response $response)
    {
        $this->requestEnd($request);
        if (isset($this->exceptionDecorator)) {
            $response->status($throwable->getCode());
            $response->send($this->exceptionDecorator->throwException($request, $throwable));
            return true;
        }

        return false;
    }

    /**
     * 处理错误
     * @param $e
     * @param $request
     * @param $response
     */
    private function handlerException($e, $request, $response)
    {

        if (!$this->requestError($e, $request, $response)) {

            if ($e instanceof NoFoundException) {

            } else if ($e instanceof MethodNotAllowException) {

            } else if ($e instanceof PHPException) {

            } else {

                $e = new PHPException($request, $response);
            }

            $e->throwException($e);
        }
    }


    public function run()
    {
        $this->server->start();
    }

}