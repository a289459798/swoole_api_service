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
    private $routes;

    public function __construct()
    {

    }

    /**
     * @param array $routes
     */
    public function loadRoute(Array $routes)
    {
        $this->routes = $routes;
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
                    }

                });
            } else {
                $r->addRoute(strtoupper($route[0]), $route[1], $route[2]);

            }
        }

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

                if (!$app->isSecurityRoute($pathInfo, $request, $response)) {
                    throw new ForbiddenException($request, $response);
                    break;
                }

                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
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