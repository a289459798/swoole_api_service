<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 00:28
 */

namespace Bijou\Example;


class User
{

    public function getInfo($params)
    {
        return json_encode([
            id => $params['id'],
            name => "zhangzy"
        ]);
    }
}