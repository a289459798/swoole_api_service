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
    public function getInfo($id)
    {

        return json_encode([
            'id' => $id
        ]);
    }

    public function create($body, $formData)
    {
        return json_encode([
            'body' => $body,
            'form' => $formData
        ]);
    }
}