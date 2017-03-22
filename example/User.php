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
    /**
     * 获取用户信息
     * @param int $id
     * @return string
     */
    public function getInfo($id)
    {
        return [
            'id' => $id
        ];
    }

    /**
     * 创建用户
     * @param $body
     * @param $formData
     * @return string
     */
    public function create($body, $formData)
    {
        return [
            'body' => $body,
            'form' => $formData
        ];
    }
}