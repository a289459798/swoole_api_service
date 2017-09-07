<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/20
 * Time: 18:07
 */

namespace Bijou\Manager;


use Bijou\Interfaces\IService;

class ServiceManager
{
    private $manager;

    public function __construct()
    {
        $this->manager = [];
    }

    public function addService(IService $service)
    {
        $this->manager[get_class($service)] = $service;
    }


    public function onCommand($process)
    {

        $manager = $this->manager;
        swoole_event_add($process->pipe, function($pipe) use($process, $manager) {

            $message = json_decode($process->read(), true);
            if (isset($manager[$message['service']])) {

                $manager[$message['service']]->onCommand($message['action'], $message['data']);
            }
            $process->exit(0);
        });
    }
}