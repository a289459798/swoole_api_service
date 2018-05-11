<?php

namespace Bijou\Core\Interfaces;


interface IService
{

    /**
     *
     * @param $action
     * @param array $data
     * @return mixed
     */
    public function onCommand($action, Array $data);

}