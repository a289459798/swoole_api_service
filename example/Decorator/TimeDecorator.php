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

    public function setRunTime($time)
    {
        echo "api:" . $this->getRequest()->getApi() . "  fd:" . $this->getRequest()->getClient() . "   运行时间:" . $time . '\r\n';
    }
}