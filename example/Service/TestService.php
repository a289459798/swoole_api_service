<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/20
 * Time: 22:24
 */

namespace Bijou\Example\Service;


use Bijou\Interfaces\ServiceInterface;

class TestService implements ServiceInterface
{
    /**
     *
     * @param $action
     * @param array $data
     * @return mixed
     */
    public function onCommand($action, Array $data)
    {
        switch ($action) {
            case 'action1':

                var_dump($data);
                break;
            case 'action2':
                var_dump($data);
                break;
        }
    }
}