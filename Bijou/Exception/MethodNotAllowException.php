<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 09:15
 */

namespace Bijou\Exception;

use Swoole\Http\Request;
use Swoole\Http\Response;

class MethodNotAllowException extends BijouException
{

    public function __construct(Request $request, Response $response)
    {
        parent::__construct('Method Not Allowed', 405, $request, $response);
    }

}