<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/21
 * Time: 15:54
 */

namespace Bijou\Interfaces;


interface ExportApiInterface
{
    /**
     * @param array $apis
     * @return mixed
     */
    public function export(Array $apis);
}