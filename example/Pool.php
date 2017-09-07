<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:28
 */

namespace Bijou\Example;


use Bijou\Controller;
use Bijou\Pool\OPool;

class Pool extends Controller
{

    public function mysql()
    {
        $this->pool(\Bijou\Example\Driver\Mysql::class);
        return [];
    }

}