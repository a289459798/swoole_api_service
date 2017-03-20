<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/16
 * Time: 23:48
 */

namespace Bijou;

use Bijou\Exception\MethodNotAllowException;
use Bijou\Exception\NoFoundException;
use Bijou\Exception\PHPException;
use FastRoute;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Swoole\Http\Request;
use Swoole\Http\Response;

class Route
{

    private $dispatcher;

    public function __construct()
    {

    }

    public function loadRoute(Array $routes)
    {
        $this->dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) use ($routes) {

            foreach ($routes as $group => $route) {
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
        });

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param App $app
     * @throws MethodNotAllowException
     * @throws NoFoundException
     * @throws PHPException
     */
    public function dispatch(Request $request, Response $response, App $app)
    {

        $app->requestStart($request);
        $method = $request->server['request_method'];
        $pathInfo = $request->server['path_info'];

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
                // ... call $handler with $vars
                if(!is_callable($handler)) {
                    throw new PHPException($request, $response);
                }
                $handlerObject = new $handler[0]($request, $response);
                $response->header("Content-Type", "application/json");
                $response->end(call_user_func_array([$handlerObject, $handler[1]], $vars));
                break;
        }

        $app->requestEnd($request);
    }

}