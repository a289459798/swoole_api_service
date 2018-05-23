<?php

namespace Bijou\Core\Exception;

use Bijou\Components\Http\Request;
use Bijou\Components\Http\Response;

class PHPException extends BijouException
{

    public function __construct(Request $request, Response $response)
    {
        parent::__construct('Server error', 500, $request, $response);
    }
}