<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/20
 * Time: 18:07
 */

namespace Bijou\Manager\File;


use Bijou\Exception\PHPException;

abstract class FileManager
{
    /**
     * @var File
     */
    protected $file;

    public function type($type) {
        return $this;
    }

    /**
     * 支持最大上传大小，单位为B
     * @param $size
     * @return $this
     * @throws \Exception
     */
    public function max($size) {
        if ($this->file->size() > $size) {
            throw new \Exception("文件大小超出限制", 500);
            exit();
        }
        return $this;
    }

    abstract public function move($path, $filename);

}