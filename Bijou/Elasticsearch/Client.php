<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/6/26
 * Time: 17:56
 */

namespace Bijou\Elasticsearch;


use Bijou\Http\CoClient;

class Client
{

    private $index = "_all";
    private $type;
    private $field = [];
    private $condition;
    private $id;
    /**
     * @var CoClient
     */
    private $client;

    public static function create()
    {
        return new static();
    }

    public function build()
    {

        $this->client = CoClient::create()
            ->setIp("192.168.1.104")
            ->setPort(9200)
            ->keepAlive()
            ->build();
        return $this;
    }

    /**
     * 设置索引
     * @param $index
     * @return $this
     */
    public function index($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * 设置分类
     * @param $type
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    public function field(...$field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @param Array|String $condition
     * @return $this
     */
    public function condition($condition)
    {
        $this->condition = $condition;
        return $this;
    }

    private function clear()
    {
        $this->index = "_all";
        $this->type = null;
        $this->field = [];
        $this->id = null;
    }

    private function getUrl()
    {
        $url = "/";
        $url .= $this->index . "/";

        if ($this->type) {
            $url .= $this->type . "/";
        }
        if ($this->id) {
            $url .= $this->id . "/";
        }

        return $url;
    }

    private function getQuery()
    {
        $query = "?";

        if (count($this->field) > 0) {
            $fields = [];
            foreach ($this->field as $v) {
                array_push($fields, $v);
            }

            $query .= "_source=" . join(",", $fields) . '&';
        }


        if (is_array($this->condition)) {
            $conditions = [];
            foreach ($this->condition as $k => $v) {
                array_push($conditions, $k . ":" . $v);
            }

            $query .= "q=" . join(",", $conditions) . '&';
        } else if (is_string($this->condition)) {
            $query .= "q=" . $this->condition . '&';
        }

        return $query;
    }

    /**
     * 插入一条记录，适用于连接调用
     * @param $body
     */
    public function insert($body)
    {
        $data = $this->client->post($this->getUrl() . $this->getQuery(), $body);
        $this->clear();
    }

    /**
     * 获取一条数据，适用于连接调用
     * @param $id
     * @return mixed
     */
    public function fetch($id)
    {
        $this->id = $id;
        return $this->client->get($this->getUrl() . $this->getQuery());
        $this->clear();
    }

    /**
     * 搜索
     * @return mixed
     */
    public function search()
    {
        return $this->client->get($this->getUrl() . "_search" . $this->getQuery());
        $this->clear();
    }

    /**
     * 自定义get命令
     * @param $command
     * @return mixed
     */
    public function get($command)
    {
        return $this->client->get($command);
    }

    /**
     * 自定义post命令
     * @param $command
     * @param $data
     * @return mixed
     */
    public function post($command, $data)
    {
        return $this->client->post($command, $data);
    }

    public function put($command, $data)
    {
        return $this->client->put($command, $data);
    }

    public function __destruct()
    {
        $this->client->close();
        $this->client = null;
    }
}