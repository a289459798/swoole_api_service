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

class Curl extends Controller
{

    /**
     * get请求
     * @return array
     */
    public function get()
    {

//        $client = new Client();
//        $response = $this->getResponse();
//        $client->get("http://book.km.com/", function ($data) use($response) {
//
//            $response->sendText($data);
//        });

        $curl = new \Curl\Curl();
        $curl->setUserAgent("Accept", "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36
");
        $curl->get("http://m.dianping.com/shop/32655393");
        var_dump($curl->response);
        return [

        ];
    }

    public function wxsession()
    {

        $curl = new \Curl\Curl();
        $curl->get("https://api.weixin.qq.com/sns/jscode2session?appid=wxfc99a824c21bc1cc&secret=wxfc99a824c21bc1cc&js_code=JSCODE&grant_type=authorization_code");
//        var_dump($curl->response);
        return $curl->response;
    }

    public function post() {
        $client = new Client();
        $response = $this->getResponse();
        $client->post("http://192.168.59.103:9501/user/", ['a' => 1, 'b' => 2], function ($data) use($response) {

            $response->sendText($data);
        });
    }

    public function dianping($cityid = 0, $start = 1) {

        $this->startService('Bijou\Example\Service\DianPingService', 'curl', ['cityid' => $cityid, 'start' => $start]);

        return ["正在抓取数据中...."];
    }

    public function pet($cityid = 0, $start = 1, $city) {

        $this->startService('Bijou\Example\Service\DianPingPetService', 'curl', ['cityid' => $cityid, 'start' => $start, 'city' => $city]);

        return ["正在抓取数据中...."];
    }

    public function boolan($page = 1) {

        $this->startService('Bijou\Example\Service\BoolanService', 'curl', ['page' => $page]);

        return ["正在抓取数据中...."];
    }

}