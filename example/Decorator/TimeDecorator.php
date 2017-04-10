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
        $this->setRunTime($request, round($endTime - $request->server['request_time_float'], 4));
    }

    /**
     * 自定义response 的数据格式
     * @param $data
     * @return mixed
     */
    public function responseFormat($data)
    {
        $code = $data['code'] ? $data['code'] : 200;
        $message = $data['message'] ? $data['message'] : '';
        unset($data['code']);
        unset($data['message']);
        return json_encode([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
}