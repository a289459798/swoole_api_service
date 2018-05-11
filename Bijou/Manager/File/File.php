<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/20
 * Time: 18:07
 */

namespace Bijou\Manager\File;


class File
{
    private $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function size()
    {
        return $this->file['size'];
    }

    public function name()
    {
        return $this->file['name'];
    }

    public function type()
    {
        return $this->file['type'];
    }

    public function tmpName()
    {
        return $this->file['tmp_name'];
    }

    public function error()
    {
        return $this->file['error'];
    }

}