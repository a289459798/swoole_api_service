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

class DianPingPetService implements IService
{

    /**
     * @var Curl
     */
    private $curl;
    private $curl2;

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
        switch ($action) {
            case 'curl':

                $this->curl = new Curl();
                $this->curl2 = new Curl();
                $this->mysql = new \MysqliDb("192.168.1.104", "root", "root", "dianping", "3306");

                $this->getList($data['cityid'], $data['start'], $data['city']);
                break;
        }
    }

    private function getList($cityid, $start, $city)
    {


        $url = "http://mapi.dianping.com/searchshop.json?start={$start}&categoryid=25147&parentCategoryId=95&locatecityid=0&limit=50&sortid=0&cityid={$cityid}&regionid=0&maptype=0";


        $this->curl->setUserAgent("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36");
        $this->curl2->setUserAgent("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3112.113 Safari/537.36" . rand(0, 9999999));
        $this->curl->get($url);


        if (!$this->curl->error) {
            $data = json_decode($this->curl->response, true);
            if ($data) {
                foreach ($data['list'] as $v) {
                    $this->curl2->get("http://www.dianping.com/shop/{$v['id']}");

                    if (!$this->curl2->error) {

                        $html = $this->curl2->response;

                        preg_match_all('/<span.*?itemprop=\"tel\".*?>(.*?)<\/span>/is', $html, $match);


                        if ($match[1]) {
                            $tel = [];
                            foreach ($match[1] as $v2) {
                                $v2 && $v2[0] == 1 && array_push($tel, $v2);
                            }
                        }

                        preg_match('/<span.*?itemprop=\"street-address\".*?title=\".*?\".*?>(.*?)<\/span>/is', $html, $matchAddr);


                        if ($tel && count($tel) > 0) {

                            $d = [
                                'type' => $v['categoryName'],
                                'name' => $v['name'] . ($v['branchName'] != "" ? "({$v['branchName']})" : ""),
                                'addr' => $matchAddr[1],
                                'tel' => join(",", $tel),
                                'region' => urldecode($city)
                            ];

                            $res = $this->mysql->insert("pet", $d);

                        }

                    } else {
                        var_dump("http://m.dianping.com/shop/{$v['id']}");
                        var_dump("错误：" . $this->curl2->error_message);
                    }
                }


                var_dump("完成cityid: {$cityid}， start: {$start}");

                sleep(2);

                if ($data['isEnd'] == false) {
                    $this->getList($cityid, $data['nextStartIndex'], $city);
                } else {
//                    if ($cityid == 1) {
                    var_dump("采集完成");
//                    } else {
//
//                        $this->getList($cityid + 1, 1);
//                    }
                }
            }
        } else {
            var_dump($this->curl->error);
            var_dump("出错cityid: {$cityid}， start: {$start}");
        }

    }

}