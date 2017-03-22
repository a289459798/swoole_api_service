<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:28
 */

namespace Bijou\Example;


use Bijou\BijouApi;
use Bijou\Example\AsyncTask\EmailTask;

class Pool extends BijouApi
{

    public function mysql()
    {
        return $this->pool('mysql')->sleep(1000000000);
    }

}