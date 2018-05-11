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

class DianPingService implements IService
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
                $this->mysql = new \MysqliDb("192.168.31.8", "root", "root", "dianping", "3306");

                $this->getList($data['cityid'], $data['start']);
                break;
        }
    }

    private function getList($cityid, $start)
    {


        $url = "http://mapi.dianping.com/searchshop.json?start={$start}&regionid=0&categoryid=50&maptype=0&cityid={$cityid}&_=1505206683419";


        $this->curl->setUserAgent("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36");
        $this->curl2->setUserAgent("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/535.6");
        $this->curl->get($url);


        if (!$this->curl->error) {

            $data = json_decode($this->curl->response, true);
            if ($data) {

                foreach ($data['list'] as $v) {
                    $this->curl2->get("http://m.dianping.com/shop/{$v['id']}");

                    if (!$this->curl2->error) {

                        $html = $this->curl2->response;
                        preg_match_all('/<a.*?href=\"tel:(.*?)\".*?>.*?<\/a>/is', $html, $match);


                        if ($match[1]) {
                            $tel = [];
                            foreach ($match[1] as $v2) {
                                $v2 && $v2[0] == 1 && array_push($tel, $v2);
                            }
                        }

                        if ($tel && count($tel) > 0) {

                            $d = [
                                'altName' => $v['altName'],
                                'categoryName' => $v['categoryName'],
                                'name' => $v['name'],
                                'regionName' => $v['regionName'],
                                'tel' => join(",", $tel)
                            ];

                            $res = $this->mysql->insert("dian", $d);

                        }

                    } else {
                        var_dump("http://m.dianping.com/shop/{$v['id']}");
                        var_dump("错误：" . $this->curl2->error_message);
                    }
                }


                var_dump("完成cityid: {$cityid}， start: {$start}");

                sleep(2);

                if ($data['isEnd'] == false) {
                    $this->getList($cityid, $data['nextStartIndex']);
                } else {
                    if ($cityid == 1) {
                        var_dump("采集完成");
                    } else {

                        $this->getList($cityid + 1, 1);
                    }
                }
            }
        } else {
            var_dump($this->curl->error);
            var_dump("出错cityid: {$cityid}， start: {$start}");
        }

    }

}