<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 09:15
 */

namespace Bijou\Exception;


class NoFoundException extends BijouException
{

    public function throwException(\Throwable $throwable)
    {
        $this->getResponse()->status(404);
        $this->getResponse()->end(json_encode([
            'code' => 404,
            'message' => 'Not Found'
        ]));
    }

}