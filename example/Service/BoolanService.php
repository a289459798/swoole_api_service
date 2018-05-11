<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/20
 * Time: 22:24
 */

namespace Bijou\Example\Service;


use Bijou\Interfaces\IService;
use Curl\Curl;

class BoolanService implements IService
{

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var \MysqliDb
     */
    private $mysql;

    /**
     *
     * @param $action
     * @param array $data
     * @return mixed
     */
    public function onCommand($action, Array $data)
    {
        var_dump("onCommand");
        switch ($action) {
            case 'curl':

                $this->curl = new Curl();
                $this->mysql = new \MysqliDb("192.168.1.104", "root", "root", "dianping", "3306");

                $this->getList($data['page']);
                break;
        }
    }

    private function getList($page)
    {


        $url = "http://admin.boolan.com/api/enrolls?conferenceId=&limit=100&page={$page}&source=event&assign=&eventId=0&name=&position=";


        $this->curl->setUserAgent("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36");
        $this->curl->setHeader("Authorization", "bearer uRvtxNmATtpKGls3ZcE_pvTNaRy97py1Y60MSDqqjpolI6vuQcZqH-eNDUetWhka9dXZI3nbEriDPuZU07mPfkE87UrUl0yndcS3o0gaie0iXwIuhDEkJfwjZ3cN3T5DZreuzGIwWfUEAHEkxvTbVUvg6QpGMn3fLZEki4gFU7huK-uhVqULSVOon-be7YAVDhpVBMeaOpv1Jq0rRh-h2t18Bmfnq-9eu8KKS08IZRM");
        $this->curl->get($url);

        if (!$this->curl->error) {

            $data = json_decode($this->curl->response, true);
            if ($data) {

                $d = [];
                foreach ($data['data']["enrolls"] as $v) {


                    $d[] = [
                        'name' => $v["account"]['name'],
                        'phone' => $v["account"]['phone'],
                        'email' => $v["account"]['email'],
                        'company' => $v["account"]['company'],
                        'position' => $v["account"]['position'],
                        'address' => $v["account"]['address'],
                    ];

                }

                $res = $this->mysql->insertMulti("boolan", $d);


                var_dump("完成page: {$page}");

                sleep(2);

                if ($data['data']["totalPage"] > $page) {
                    $this->getList($page + 1);
                } else {
                    var_dump("采集完成");
                }
            } else {
                var_dump("page:{$page}, 无数据");
            }
        } else {
            var_dump($this->curl->error);
            var_dump("出错page:{$page}");
        }

    }

}