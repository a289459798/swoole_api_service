<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:28
 */

namespace Bijou\Example;


use Bijou\BijouApi;
use Bijou\Example\AsyncTask\EmailTask;

class Feed extends BijouApi
{

    /**
     * 获取信息
     * @param int $id
     * @return string
     */
    public function getInfo($id)
    {

        $this->getResponse()->sent("12121212");

        return ['id' => $id];
    }

    /**
     * 获取发帖用户信息
     * @param $id
     * @return mixed
     */
    public function getUser($id)
    {

        return $this->invokeApi(['\Bijou\Example\User', 'getInfo'], [$id]);
    }

    /**
     * 异步任务发送邮件
     * @return string
     */
    public function postEmail()
    {
        $this->addAsyncTask(new EmailTask('zhangzy@bijou.com'));
        return '123';
    }

    /**
     * 执行后台任务
     * @return string
     */
    public function service()
    {
        $this->startService('Bijou\Example\Service\TestService', 'action1', ['data' => 'data1']);
        $this->startService('Bijou\Example\Service\TestService', 'action2', ['data' => 'data2']);
        return '123';
    }

    /**
     * 发表帖子
     * @return string
     */
    public function create()
    {
        return [
            'post' => $this->getRequest()->post,
            'data' => $this->getRequest()->getBody(),
        ];
    }

    /**
     * @Ignore
     * @return bool
     */
    public function check()
    {
        return false;
    }

}