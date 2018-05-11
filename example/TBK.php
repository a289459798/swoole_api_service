<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:28
 */

namespace Bijou\Example;


use Bijou\Controller;
use Bijou\Http\Client;

class TBK extends Controller
{

    const appkey = "24650000";
    const appSecret = "bce117ebf61d8819770cb68d3cb61e20";

    /**
     * 搜索淘宝客商品
     * @return array
     */
    public function search($query)
    {

        $curl = new \Curl\Curl();
        $curl->get("http://gw.api.taobao.com/router/rest", $this->getGlobalParams("taobao.tbk.item.get", [

            "fields" => "num_iid,title,item_url",
            "q" => urldecode($query)
        ]));
        $data = json_decode($curl->response, true);
        return $data;
    }

    public function getLink($num_iid) {

        $curl = new \Curl\Curl();
        $curl->get("http://gw.api.taobao.com/router/rest", $this->getGlobalParams("taobao.tbk.item.convert", [

            "fields" => "num_iid,click_url",
            "num_iids" => $num_iid,
            "sub_pid" => "mm_27122901_38016001_139000042",
            "adzone_id" => "推广1"
        ]));
        $data = json_decode($curl->response, true);
        return $data;
    }

    private function getGlobalParams($api, $data) {

        $common = array_merge([
            "method" => $api,
            "app_key" => self::appkey,
            "sign_method" => "md5",
            "timestamp" => date("Y-m-d h:i:s"),
            "v" => "2.0",
            "format" => "json"
        ], $data);

        // 将参数排序
        ksort($common);

        // 拼接
        $commonStr = preg_replace("/[=&]/i", "", urldecode(http_build_query($common)));

        var_dump($commonStr);
        $sign = strtoupper(bin2hex(md5(self::appSecret . $commonStr . self::appSecret, true)));

        var_dump($common + ["sign" => $sign]);
        return $common + ["sign" => $sign];
    }


}