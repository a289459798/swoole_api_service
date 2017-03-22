<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 11:40
 */

namespace Bijou\Example\Decorator;


class ResponseDecorator extends \Bijou\Decorator\ResponseDecorator
{

    /**
     * 自定义response 的数据格式
     * @param $data
     * @return mixed
     */
    public function format($data)
    {
        return json_encode([
            'code' => isset($data['code']) ? $data['code'] : 200,
            'message' => isset($data['message']) ? $data['message'] : '',
            'data' => $data
        ]);
    }
}