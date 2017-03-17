<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 09:15
 */

namespace Bijou\Exception;


class MethodNotAllowException extends BijouException
{
    public function throwException()
    {
        $this->getResponse()->status(405);
        $this->getResponse()->end(json_encode([
            'code' => 405,
            'message' => 'Method Not Allowed'
        ]));
    }

}