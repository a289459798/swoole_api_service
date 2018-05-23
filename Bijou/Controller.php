<?php

namespace Bijou;

use Bijou\Core\Interfaces\IAsyncTask;
use Bijou\Components\Http\Request;
use Bijou\Components\Http\Response;
use Bijou\Manager\Pool\OPool;

abstract class Controller
{

    private $app;
    private $request;
    private $response;
    private $poolInstance;  // 对象池取到的对象实例

    /**
     * Controller constructor.
     * @param App $app
     * @param Request $request
     * @param Response $response
     */
    public function __construct($app, $request, $response)
    {
        $this->app = $app;
        $this->request = $request;
        $this->response = $response;
        $this->poolInstance = [];
    }

    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return \Bijou\Components\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Bijou\Components\Http\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 接口间的调用
     * @param array $callback
     * @param array $vars
     * @return mixed
     */
    public function dispatch(Array $callback, Array $vars)
    {

        $handle = new $callback[0]($this->app, $this->request, $this->response);
        return call_user_func_array([$handle, $callback[1]], $vars);
    }

    public function pool($className, ...$args)
    {

        $obj = OPool::getInstance()->pop($className, ...$args);
        array_push($this->poolInstance, $obj);
        return $obj;
    }

    /**
     * 执行一个异步任务
     * @param IAsyncTask $asyncTask
     */
    public function addAsyncTask(IAsyncTask $asyncTask)
    {
        $this->app->addAsyncTask($asyncTask);
    }

    /**
     * 给常驻进程发送执行命令
     * @param string $classPath 注册service的class 路径
     * @param string $action
     * @param array $data 传递的数据
     */
    public function startService($classPath, $action, Array $data)
    {
        $this->app->startService($classPath, $action, $data);
    }

    public function __destruct()
    {
        foreach ($this->poolInstance as $v) {
            OPool::getInstance()->push($v);
        }
    }
}