<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:28
 */

namespace Bijou\Example;


use Bijou\BijouApi;

class Feed extends BijouApi
{

    public function getInfo($id)
    {

        $this->getRequest()->server;

        return json_encode(
            ['id' => $id]
        );
    }
}