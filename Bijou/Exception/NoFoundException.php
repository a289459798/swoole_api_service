<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 09:15
 */

namespace Bijou\Exception;

use Bijou\Http\Request;
use Bijou\Http\Response;

class NoFoundException extends BijouException
{

    public function __construct(Request $request, Response $response)
    {
        parent::__construct('Not Found', 404, $request, $response);
    }


}