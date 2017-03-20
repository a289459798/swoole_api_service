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
    public function doInBackground($from_id);
    public function onFinish();
}