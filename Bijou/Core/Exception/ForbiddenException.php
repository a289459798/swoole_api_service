<?php

namespace Bijou\Core\Exception;

use Bijou\Components\Http\Request;
use Bijou\Components\Http\Response;

class ForbiddenException extends BijouException
{

    public function __construct(Request $request, Response $response)
    {
        parent::__construct('403 Forbidden', 403, $request, $response);
    }


}