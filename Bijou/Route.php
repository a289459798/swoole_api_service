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
    private $dispatcherVersion = [];
    private $routerVersion = [];
    private $securityRouters;
    private $cacheRouters;
    private $routes;

    public function __construct()
    {
        $this->routes = [];
        $this->securityRouters = [];
        $this->cacheRouters = [];
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

                $r->addGroup($group, function (RouteCollector $r) use ($route, $group) {

                    foreach ($route as $rou) {
                        $this->parseRoute($r, $rou, $group);
                    }

                });
            } else {
                $this->parseRoute($r, $route);
            }
        }

        foreach ($this->routerVersion as $version => $route) {
            $this->dispatcherVersion[$version] = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) use ($route) {

                if ($route['group']) {
                    $r->addGroup($route['group'], function (RouteCollector $r) use ($route) {

                        $r->addRoute($route['router'][0], $route['router'][1], $route['router'][2]);

                    });
                } else {
                    $r->addRoute($route['router'][0], $route['router'][1], $route['router'][2]);
                }

            });
        }

    }

    /**
     * @param RouteCollector $r
     * @param $route
     * @param string $group
     */
    private function parseRoute(RouteCollector $r, $route, $group = "")
    {


        if (isset($route['version'])) {
            if (!is_array($this->routerVersion[$route['version']])) {
                $this->routerVersion[$route['version']] = [];
            }
            $this->routerVersion[$route['version']] += ['group' => $group, 'router' => $route];
            return;
        }
        $r->addRoute(strtoupper($route[0]), $route[1], $route[2]);

        $callback = join("_", $route[2]);
        if (isset($route['security'])) {
            $this->securityRouters[$callback] = $route['security'];
        }

        if ($route[0] == 'GET' && isset($route['cache'])) {
            $this->cacheRouters[$callback] = $route['cache'];
        }
    }

    /**
     * @param callable $callback
     * @return bool|callable
     */
    public function getSecurityRouter(callable $callback)
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
        if ($handler = $this->getSecurityRouter($callback)) {

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
     * 检查是否缓存
     * @param $callback
     * @param App $app
     * @param Request $request
     * @return bool
     */
    public function checkCache($callback, App $app, Request $request)
    {
        $cacheManager = $app->getCacheManager();
        if ($cacheManager && isset($this->cacheRouters[join("_", $callback)]) && $request->getMethod() == 'GET') {

            $expire = $this->cacheRouters[join("_", $callback)];
            if ($cacheManager) {
                return $cacheManager->readCache($request->getApi(), $expire);
            }
        }
        return false;
    }

    /**
     * 写缓存
     * @param $callback
     * @param App $app
     * @param Request $request
     * @param $data
     */
    public function writeCache($callback, App $app, Request $request, $data)
    {
        $cacheManager = $app->getCacheManager();
        if ($cacheManager && isset($this->cacheRouters[join("_", $callback)]) && $request->getMethod() == 'GET') {
            $cacheManager->writeCache($request->getApi(), $data);
        }
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

        $method = $request->getMethod();
        $pathInfo = $request->getApi();
        $version = $request->getVersion();
        krsort($this->dispatcherVersion);

        if ($version) {
            if (isset($this->dispatcherVersion[$version])) {
                $routeInfo = $this->dispatcherVersion[$version]->dispatch($method, $pathInfo);
            } else {
                foreach ($this->dispatcherVersion as $ver => $dispatcher) {

                    if ($ver > $version) {
                        continue;
                    }
                    $routeInfo = $dispatcher->dispatch($method, $pathInfo);
                    if ($routeInfo) {
                        break;
                    }
                }
            }

        }
        if (!$routeInfo) {
            $routeInfo = $this->dispatcher->dispatch($method, $pathInfo);
        }

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

                $requestStart = $app->requestStart($request);
                if (true !== $requestStart) {

                    $response->send($requestStart);
                    $app->requestEnd($request);
                    return;
                }

                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                if (!$this->isSecurityRoute($handler, $app, $request, $response)) {
                    throw new ForbiddenException($request, $response);
                    break;
                }

                if ($cache = $this->checkCache($handler, $app, $request)) {
                    $response->send($cache);
                    $app->requestEnd($request);
                    break;
                }

                // ... call $handler with $vars
                if (!is_callable($handler)) {
                    throw new PHPException($request, $response);
                }
                $handlerObject = new $handler[0]($app, $request, $response);

                if ($request->isPost()) {
                    $vars += [$request->postData()];
                }

                $responseData = call_user_func_array([$handlerObject, $handler[1]], $vars);
                $responseData && $response->send($responseData);
                $this->writeCache($handler, $app, $request, $responseData);
                $app->requestEnd($request, $responseData);
                break;
        }

    }

}