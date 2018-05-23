<?php
namespace Bijou\Components\Http;

use Bijou\Manager\File\FileManager;
use Bijou\Manager\File\MultiFileManager;
use Bijou\Manager\File\SingleFileManager;
use Swoole\Http;

/**
 * Class Request
 * @package Bijou\Http
 *
 * @property $get
 * @property $post
 * @property $header
 * @property $server
 * @property $cookie
 * @property $files
 * @property $fd
 */
class Request
{
    private $request;
    private $header;
    public $get;
    public $post;

    public function __construct(Http\Request $request)
    {
        $this->request = $request;
        $this->get = isset($this->request->get) ? $this->request->get : [];
        $this->post = isset($this->request->post) ? $this->request->get : [];
    }

    /**
     * 获取请求的客户端标识
     * @return mixed
     */
    public function getClient()
    {
        return $this->request->fd;
    }

    public function getMethod()
    {
        return $this->request->server['request_method'];
    }

    /**
     * 是否为post请求
     * @return bool
     */
    public function isPost()
    {
        return strtolower($this->getMethod()) == 'post';
    }

    /**
     * 是否为get请求
     * @return bool
     */
    public function isGet()
    {
        return strtolower($this->getMethod()) == 'get';
    }

    /**
     * 判断请求方式
     * @param string $method 请求方式(小写)
     * @return bool
     */
    public function isMethod($method)
    {
        return strtolower($this->getMethod()) == $method;
    }

    /**
     * 判断是否为表单提交
     * @return bool
     */
    public function isForm()
    {

        if ($this->getHeader()->getContentType() == 'application/x-www-form-urlencoded') {

            return true;
        }

        if (false !== strpos($this->getHeader()->getContentType(), "multipart/form-data")) {
            return true;
        }
        return false;
    }

    /**
     * 获取post请求过来的数据，json会自动decode
     * @return mixed
     */
    public function postData()
    {
        if ($this->isForm()) {

            return $this->request->post;
        }
        return json_decode($this->getBody(), true);
    }

    /**
     * @return Header
     */
    public function getHeader()
    {

        if (!$this->header) {
            $this->header = new Header($this->request->header);
        }

        return $this->header;
    }

    public function getVersion()
    {
        return $this->request->header['version'];
    }

    public function getApi()
    {
        return $this->request->server['path_info'];
    }

    /**
     * 获取用户ip
     * @return mixed
     */
    public function getIp()
    {
        return $this->request->server['remote_addr'];
    }

    /**
     * 获取响应服务器端口
     * @return mixed
     */
    public function getPort()
    {
        return $this->request->server['server_port'];
    }

    public function getBody()
    {
        return $this->request->rawContent();
    }

    /**
     * @param $param
     * @return mixed
     */
    public function get($param)
    {
        return $this->request->get[$param];
    }

    /**
     * @param $param
     * @return mixed
     */
    public function post($param)
    {
        $data = [];
        if ($this->isForm()) {
            $data = $this->request->post;
        } else {
            $data = json_decode($this->getBody(), true);
        }
        return $data[$param];
    }

    /**
     * 判断是否为文件
     * @param $param
     * @return bool
     */
    public function isFile($param)
    {
        return isset($this->request->files[$param]);
    }

    /**
     * @param $param
     * @return FileManager
     */
    public function file($param)
    {
        $file = $this->request->files[$param];
        return new SingleFileManager($file);
    }

    /**
     * @return FileManager
     */
    public function files()
    {
        $files = $this->request->files;
        return new MultiFileManager($files);
    }

    public function __get($name)
    {
        if (isset($this->request->{$name})) {

            return $this->request->{$name};
        }
    }

    public function __toString()
    {
        return json_encode($this->request, true);
    }
}