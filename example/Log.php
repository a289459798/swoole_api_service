<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/17
 * Time: 00:28
 */

namespace Bijou\Example;

use Bijou\Controller;
use Bijou\Storage\Elasticsearch;

class Log extends Controller
{
    /**
     * @var Elasticsearch\Client
     */
    private $client;
    public function __construct($app, $request, $response)
    {
        parent::__construct($app, $request, $response);

        $this->client = Elasticsearch\Client::create()->build();
    }

    public function getLog($id) {

        $this->getResponse()->end($this->client->index('u')->type('user')->field("name")->fetch($id));
    }

    public function postLog($body) {

        $this->client->index('u')->type('user')->insert(json_encode($body));
        $this->getResponse()->end("成功");
    }

    public function searchLog($keyword) {

        $data = $this->client->condition(["name" => "aaa"])->search();
        $this->getResponse()->end($data);
    }
}