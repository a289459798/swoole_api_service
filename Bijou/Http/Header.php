<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/18
 * Time: 15:34
 */

namespace Bijou\Http;

class Header
{
    private $header;

    public function __construct(Array $header)
    {
        $this->header = $header;
    }

    public function getAuthorization() {

        return $this->header['authorization'];
    }

    public function __get($name)
    {
        if (isset($this->header[$name])) {

            return $this->header[$name];
        }
    }

    public function __toString()
    {
        return json_encode($this->header, true);
    }
}