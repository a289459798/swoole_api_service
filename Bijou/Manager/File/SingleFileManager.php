<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/20
 * Time: 18:07
 */

namespace Bijou\Manager\File;


class SingleFileManager extends FileManager
{
    public function __construct($file)
    {
        if(!$file) {
            throw new \Exception("上传文件不存在", 500);
            exit();
        }
        $this->file = new File($file);
    }

    public function move($path, $filename)
    {

    }

}