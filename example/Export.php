<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:28
 */

namespace Bijou\Example;


use Bijou\Controller;
use Bijou\Example\AsyncTask\ExportApi;

class Export extends Controller
{

    /**
     * @return string
     * @Ignore
     */
    public function getApi()
    {

        $this->addAsyncTask(new ExportApiTask(new ExportApi(), $this->getApp()->getRoutes()));
        return "接口正在导出，请查看文件";
    }

}