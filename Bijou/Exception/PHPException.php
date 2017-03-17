<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 09:15
 */

namespace Bijou\Exception;


class PHPException extends BijouException
{

    public function throwException(\Throwable $throwable)
    {
        $this->getResponse()->status(500);
        $this->getResponse()->end(json_encode([
            'code' => 500,
            'message' => 'Server error'
        ]));
    }

}