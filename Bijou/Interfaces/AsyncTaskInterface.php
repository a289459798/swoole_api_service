<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/20
 * Time: 15:43
 */

namespace Bijou\Interfaces;


interface AsyncTaskInterface
{
    /**
     * 异步执行
     * @param $from_id
     * @return mixed
     */
    public function doInBackground($from_id);

    /**
     * 任务完成后回调
     * @return mixed
     */
    public function onFinish();
}