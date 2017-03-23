<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 11:40
 */

namespace Bijou\Example\Decorator;

use Bijou\Decorator\RunTimeDecorator;
use Bijou\Http\Request;

class TimeDecorator extends RunTimeDecorator
{
    private $requests;

    public function __construct()
    {
        $this->requests = [];
    }

    private function setRunTime($request, $time)
    {
        var_dump("api:" . $request->getApi() . "   运行时间:" . $time);
    }

    private function getCurrentTime()
    {
        list ($msec, $sec) = explode(" ", microtime());
        return (float)$msec + (float)$sec;
    }

    /**
     * 请求开始之前回调，可验证请求的安全性，返回true 正常请求，否则 请求终止，并输出返回内容
     * @param Request $request
     * @return bool
     */
    public function requestStart(Request $request)
    {
        $this->requests[$request->getClient()] = $this->getCurrentTime();

        return true;
    }

    /**
     * 请求完成之后回调之后回调
     * @param Request $request
     * @param $data
     * @return mixed
     */
    public function requestEnd(Request $request, $data = null)
    {
        $endTime = $this->getCurrentTime();
        $this->setRunTime($request, round($endTime - $this->requests[$request->getClient()], 4));
        unset($this->requests[$request->getClient()]);

    }

    /**
     * 自定义response 的数据格式
     * @param $data
     * @return mixed
     */
    public function responseFormat($data)
    {
        return json_encode([
            'code' => isset($data['code']) ? $data['code'] : 200,
            'message' => isset($data['message']) ? $data['message'] : 200,
            'data' => $data
        ]);
    }
}