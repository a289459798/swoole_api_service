<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/20
 * Time: 15:43
 */

namespace Bijou\Interfaces;


interface ServiceInterface
{

    /**
     *
     * @param $action
     * @param array $data
     * @return mixed
     */
    public function onCommand($action, Array $data);

}