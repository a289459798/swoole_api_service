<?php

namespace Bijou\Components\CoHttp;

use Bijou\Swoole\ServerSetting;

abstract class Server
{
    protected $host = "0.0.0.0";

    protected $port = 9503;

    protected $mode = \SWOOLE_BASE;

    protected $socketType = \SWOOLE_SOCK_TCP;

    /**
     * @var ServerSetting
     */
    public $setting;

    /**
     * @var \swoole_server
     */
    public $server;

    /**
     * TcpServer constructor.
     * @param string $host
     * @param int $port
     * @param int $mode
     * @param int $socketType
     */
    public function __construct($host = '', $port = 0, $mode = 0, $socketType = 0)
    {
        if ($host) $this->host = $host;
        if ($port) $this->port = $port;
        if ($mode) $this->mode = $mode;
        if ($socketType) $this->socketType = $socketType;
        $this->server = new \swoole_http_server($this->host, $this->port, $this->mode, $this->socketType);
        $this->setting = new ServerSetting($this->server);
        $this->bind();
    }

    abstract public function run();

    public function runDemo()
    {
        $this->server->on("start", function ($server) {
            echo "Swoole http server is started at http://127.0.0.1:{$this->port}\n";
        });

        $this->server->on("request", function (\swoole_http_request $request, \swoole_http_response $response) {
            $response->header("Content-Type", "text/plain");
            $this->server->task(['x', 'y'], null, function (\swoole_http_server $server, $task_id, $data) use ($response) {
                $response->end(json_encode($data));
            });
        });

        $this->server->on('task', function (\swoole_http_server $server, $task_id, $reactor_id, $data) {
            return json_encode([
                'task-id' => $task_id,
                'reactor-id' => $reactor_id,
                'data' => $data
            ]);
        });
        $this->server->on('finish', function (\swoole_http_server $server, $task_id, $data) {
            echo "AsyncTask[$task_id] finished: {$data}\n";
        });
        $this->setting->setWorkerNum(1);
        $this->setting->setTaskIPCMode(1);
        $this->setting->setTaskWorkerNum(1);
        $this->server->set($this->setting->getSettings());
        $this->server->start();
    }

    /**
     * @return mixed
     */
    public function start()
    {
        return $this->server->start();
    }

    /**
     *
     */
    protected function bind()
    {
        $refClass = new \ReflectionClass($this);
        $methods = $refClass->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $name = $method->getShortName();
            if (substr($name, 0, 2) === 'on') {
                $this->server->on(substr($name, 2), [$this, $name]);
            }
        }
    }

    /**
     * @param \swoole_server $server
     */
    public function onStart(\swoole_server $server)
    {
    }

    /**
     * @param \swoole_server $server
     */
    public function onShutdown(\swoole_server $server)
    {
    }

    /**
     * @param \swoole_server $server
     */
    public function onManagerStart(\swoole_server $server)
    {
    }

    /**
     * @param \swoole_server $server
     */
    public function onManagerStop(\swoole_server $server)
    {
    }

    /**
     * @param \swoole_server $server
     * @param $worker_id
     */
    public function onWorkerStart(\swoole_server $server, $worker_id)
    {
    }

    /**
     * @param \swoole_server $server
     * @param $worker_id
     */
    public function onWorkerStop(\swoole_server $server, $worker_id)
    {
    }

    /**
     * @param \swoole_server $server
     * @param $worker_id
     */
    public function onWorkerExit(\swoole_server $server, $worker_id)
    {
    }

    /**
     * @param \swoole_server $server
     * @param $worker_id
     * @param $worker_pid
     * @param $exit_code
     * @param $signal
     */
    public function onWorkerError(\swoole_server $server, $worker_id, $worker_pid, $exit_code, $signal)
    {
    }

    /**
     * @param \swoole_server $server
     * @param $task_id
     * @param $data
     */
    public function onFinish(\swoole_server $server, $task_id, $data)
    {
    }

    /**
     * @param \swoole_server $server
     * @param $src_worker_id
     * @param $message
     */
    public function onPipeMessage(\swoole_server $server, $src_worker_id, $message)
    {
    }
}
