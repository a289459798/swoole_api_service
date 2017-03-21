<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:28
 */

namespace Bijou\Example;


use Bijou\BijouApi;
use Bijou\Example\AsyncTask\ExportApi;

class Export extends BijouApi
{

    /**
     * @return string
     * @Ignore
     */
    public function getApi()
    {
        $this->exportApi(new ExportApi());
        return "接口正在导出，请查看文件";
    }

}