<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 10:28
 */

namespace Bijou\Example;


use Bijou\BijouApi;
use Bijou\Http\Client;

class Curl extends BijouApi
{

    /**
     * get请求
     * @return array
     */
    public function get()
    {

        $client = new Client();
        $response = $this->getResponse();
        $client->get("http://book.km.com/", function ($data) use($response) {

            $response->sendText($data);
        });
    }

    public function post() {
        $client = new Client();
        $response = $this->getResponse();
        $client->post("http://192.168.59.103:9501/user/", ['a' => 1, 'b' => 2], function ($data) use($response) {

            $response->sendText($data);
        });
    }

}