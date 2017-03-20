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

class Feed extends BijouApi
{

    public function getInfo($id)
    {

        $this->getResponse()->sent("12121212");

        return json_encode(
            ['id' => $id]
        );
    }

    public function postEmail()
    {
        $this->addAsyncTask(new EmailTask('zhangzy@bijou.com'));
        return '123';
    }

    public function create()
    {
        return json_encode([
            'post' => $this->getRequest()->post,
            'data' => $this->getRequest()->getBody(),
        ]);
    }

    public function check()
    {
        return false;
    }

}