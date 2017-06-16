<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:28
 */

namespace Bijou\Example;


use Bijou\Controller;
use Bijou\Example\AsyncTask\EmailTask;

class Pool extends Controller
{

    public function mysql()
    {
        return $this->pool('mysql')->sleep(1000000000);
    }

}