<?php
namespace Bijou\Core\Interfaces;


interface IAsyncTask
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