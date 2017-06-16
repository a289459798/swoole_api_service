<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/20
 * Time: 16:17
 */

namespace Bijou\Manager;


use Bijou\Interfaces\AsyncTaskInterface;
use Swoole\Http\Server;

class TaskManager
{

    public function __construct()
    {
    }

    public function addTask(Server $server, AsyncTaskInterface $asyncTask)
    {
        $server->task($asyncTask);
    }

    public function onTask(Server $server, $task_id, $from_id, $data)
    {
        $data->doInBackground($from_id);
        $server->finish($data);
    }

    public function onFinish(Server $server, $task_id, $data)
    {
        $data->onFinish();
    }

}