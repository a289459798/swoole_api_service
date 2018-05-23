<?php

namespace Bijou\Components\Http;

/**
 * Class Header
 * @package Bijou\Http
 */
class Header
{
    private $header;

    public function __construct(Array $header)
    {
        $this->header = $header;
    }

    public function getAuthorization()
    {

        return $this->header['authorization'];
    }

    public function getContentType()
    {

        return $this->header['content-type'];
    }

    public function __get($name)
    {
        if (isset($this->header[$name])) {
            return $this->header[$name];
        } else {
            return null;
        }
    }

    public function __toString()
    {
        return json_encode($this->header, true);
    }
}