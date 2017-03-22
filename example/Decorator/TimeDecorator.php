<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 11:40
 */

namespace Bijou\Example\Decorator;

use Bijou\Decorator\RunTimeDecorator;

class TimeDecorator extends RunTimeDecorator
{
    private $requests;

    public function __construct()
    {
        $this->requests = [];
    }

    private function setRunTime($time)
    {
        var_dump("api:" . $this->getRequest()->getApi() . "   运行时间:" . $time);
    }

    private function getCurrentTime()
    {
        list ($msec, $sec) = explode(" ", microtime());
        return (float)$msec + (float)$sec;
    }

    /**
     * 请求开始之前回调，可验证请求的安全性，返回true 正常请求，否则 请求终止，并输出返回内容
     * @return bool
     */
    public function requestStart()
    {
        $this->requests[$this->getRequest()->getClient()] = $this->getCurrentTime();

        return true;
//        return [
//            '验证错误'
//        ];
    }

    /**
     * 请求完成之后回调之后回调
     * @return mixed
     */
    public function requestEnd()
    {
        $endTime = $this->getCurrentTime();
        $this->setRunTime(round($endTime - $this->requests[$this->getRequest()->getClient()], 4));
        unset($this->requests[$this->getRequest()->getClient()]);
    }
}