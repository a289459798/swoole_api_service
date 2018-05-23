<?php

namespace Bijou\Core\Exception;

use Bijou\Components\Http\Request;
use Bijou\Components\Http\Response;

class MethodNotAllowException extends BijouException
{

    public function __construct(Request $request, Response $response)
    {
        parent::__construct('Method Not Allowed', 405, $request, $response);
    }

}