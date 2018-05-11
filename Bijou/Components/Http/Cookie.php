<?php

namespace Bijou\Http;

class Cookie
{

    private $cookies;
    private $setting;

    public function __construct()
    {
        $this->cookies = [];
        $this->setting = [];
    }

    public function setSetting($expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
    {

    }

    public function set($name, $value)
    {
        list($expire, $path, $domain, $secure, $httponly) = $this->getSetting();
        $this->cookies[$name] = [
            'name' => $name,
            'value' => $value,
            'expire' => $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
        ];
    }

    public function get($name)
    {
        return $this->cookies[$name];
    }

    public function getAll()
    {
        return $this->cookies;
    }

    public function getSetting()
    {
        return array_replace([
            'expire' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => false
        ], $this->setting);
    }
}