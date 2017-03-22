<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/16
 * Time: 23:48
 */

namespace Bijou;

use Bijou\Exception\ForbiddenException;
use Bijou\Exception\MethodNotAllowException;
use Bijou\Exception\NoFoundException;
use Bijou\Exception\PHPException;
use Bijou\Http\Request;
use Bijou\Http\Response;
use FastRoute;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

class Route
{

    private $dispatcher;
    private $securityRouters;
    private $routes;

    public function __construct()
    {
        $this->routes = [];
        $this->securityRouters = [];
    }

    /**
     * @param array $routes
     */
    public function loadRoute(Array $routes)
    {
        $this->routes += $routes;
        $this->dispatcher = FastRoute\simpleDispatcher([$this, 'simpleDispatcher']);
    }

    /**
     * @param RouteCollector $r
     */
    public function simpleDispatcher(RouteCollector $r)
    {

        foreach ($this->routes as $group => $route) {
            if (!is_int($group)) {

                $r->addGroup($group, function (RouteCollector $r) use ($route) {

                    foreach ($route as $rou) {
                        $r->addRoute(strtoupper($rou[0]), $rou[1], $rou[2]);
                        $this->parseRoute($rou);
                    }

                });
            } else {
                $r->addRoute(strtoupper($route[0]), $route[1], $route[2]);
                $this->parseRoute($route);
            }
        }

    }

    private function parseRoute($route)
    {
        if (isset($route['security'])) {
            $this->securityRouters[join("_", $route[2])] = $route['security'];
        }
    }

    /**
     * @param callable $callback
     * @return bool|callable
     */
    public function getSecurityRouters(callable $callback)
    {
        if (isset($this->securityRouters[join("_", $callback)])) {
            return $this->securityRouters[join("_", $callback)];
        }
        return false;
    }

    /**
     * 验证是否是安全
     * @param $callback
     * @param App $app
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    private function isSecurityRoute($callback, App $app, Request $request, Response $response)
    {
        if ($handler = $this->getSecurityRouters($callback)) {

            $handlerObject = new $handler[0]($app, $request, $response);
            return call_user_func([$handlerObject, $handler[1]]);
        }
        return true;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }


    /**
     * @param Request $request
     * @param Response $response
     * @param App $app
     * @throws MethodNotAllowException
     * @throws NoFoundException
     * @throws PHPException
     * @throws ForbiddenException
     */
    public function dispatch(Request $request, Response $response, App $app)
    {


        $requestStart = $app->requestStart($request);
        if (true !== $requestStart) {

            $response->send($requestStart);
            return;
        }
        $method = $request->getMethod();
        $pathInfo = $request->getApi();

        $routeInfo = $this->dispatcher->dispatch($method, $pathInfo);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                throw new NoFoundException($request, $response);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                // ... 405 Method Not Allowed
                throw new MethodNotAllowException($request, $response);
                break;
            case Dispatcher::FOUND:

                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                if (!$this->isSecurityRoute($handler, $app, $request, $response)) {
                    throw new ForbiddenException($request, $response);
                    break;
                }

                // ... call $handler with $vars
                if (!is_callable($handler)) {
                    throw new PHPException($request, $response);
                }
                $handlerObject = new $handler[0]($app, $request, $response);

                if ('POST' == $method) {
                    $vars = [$request->getBody(), $request->post];
                }

                $response->send(call_user_func_array([$handlerObject, $handler[1]], $vars));
                break;
        }

        $app->requestEnd($request);
    }

}