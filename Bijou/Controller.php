<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:26
 */

namespace Bijou;

use Bijou\Interfaces\AsyncTaskInterface;
use Bijou\Http\Request;
use Bijou\Http\Response;

abstract class Controller
{

    private $app;
    private $request;
    private $response;

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
    }

    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return \Bijou\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Bijou\Http\Response
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

    public function pool($name)
    {
        return $this->app->pool($name);
    }

    /**
     * 执行一个异步任务
     * @param AsyncTaskInterface $asyncTask
     */
    public function addAsyncTask(AsyncTaskInterface $asyncTask)
    {
        $this->app->addAsyncTask($asyncTask);
    }

    /**
     * 给常驻进程发送执行命令
     * @param $classPath 注册service的class 路径
     * @param $action
     * @param array $data 传递的数据
     */
    public function startService($classPath, $action, Array $data)
    {
        $this->app->startService($classPath, $action, $data);
    }
}