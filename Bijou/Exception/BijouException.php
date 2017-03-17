<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 09:11
 */

namespace Bijou\Exception;

use Swoole\Http\Request;
use Swoole\Http\Response;

abstract class BijouException extends \Exception
{
    private $request;
    private $response;

    /**
     * BijouException constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct($request, $response)
    {
        parent::__construct();
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest() {
        return $this->request;
    }

    public function getResponse() {
        return $this->response;
    }

    abstract function throwException(\Throwable $throwable);

}