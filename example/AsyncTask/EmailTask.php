<?php

/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/20
 * Time: 16:07
 */

namespace Bijou\Example\AsyncTask;


use Bijou\Interfaces\AsyncTaskInterface;

class EmailTask implements AsyncTaskInterface
{

    private $email;
    public function __construct($email)
    {
        $this->email = $email;
    }

    public function doInBackground($from_id)
    {
        sleep(10);
        var_dump('doInBackground');
    }

    public function onFinish()
    {
        var_dump('onFinish');
    }
}