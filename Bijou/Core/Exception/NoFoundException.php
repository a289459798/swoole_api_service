<?php

namespace Bijou\Core\Exception;

use Bijou\Components\Http\Request;
use Bijou\Components\Http\Response;

class NoFoundException extends BijouException
{

    public function __construct(Request $request, Response $response)
    {
        parent::__construct('Not Found', 404, $request, $response);
    }


}