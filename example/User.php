<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 00:28
 */

namespace Bijou\Example;

use Bijou\Controller;

class User extends Controller
{
    /**
     * 获取用户信息
     * @param int $id
     * @return string
     */
    public function getUser($id)
    {
        $db = new \MysqliDb("192.168.1.104", "root", "root", "test", "3306");
        $res = $db->query("select sleep(1)");
        return [$res];

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