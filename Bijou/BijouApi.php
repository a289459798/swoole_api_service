<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:26
 */

namespace Bijou;

use Bijou\Interfaces\AsyncTaskInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;

abstract class BijouApi
{

    private $app;
    private $request;
    private $response;

    /**
     * BijouApi constructor.
     * @param App $app
     * @param Request $request
     * @param Response $response
     */
    public function __construct($app, $request, $response)
    {
        $this->app = $app;
        $this->request = new \Bijou\Http\Request($request);
        $this->response = new \Bijou\Http\Response($response);
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

    public function addAsyncTask(AsyncTaskInterface $asyncTask)
    {
        $this->app->addAsyncTask($asyncTask);
    }
}