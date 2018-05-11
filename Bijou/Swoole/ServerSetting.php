<?php

namespace Bijou\Swoole;
class ServerSetting extends SocketSetting
{

    /**
     * @param $num
     */
    public function setWorkerNum($num)
    {
        $this->setting['worker_num'] = $num;
    }

    /**
     * Worker的最大请求数(超过重启)
     * @param $num
     */
    public function setWorkerMaxRequest($num)
    {
        $this->setting['max_request'] = $num;
    }

    /**
     * 设置reactor数量
     * @param $num
     * reactor_num一般设置为CPU核数的1-4倍，在swoole中reactor_num最大不得超过CPU核数*4。
     * reactor_num必须小于或等于worker_num。如果设置的reactor_num大于worker_num，那么swoole会自动调整使reactor_num等于worker_num
     */
    public function setReactorNum($num)
    {
        $this->setting['reactor_num'] = $num;
    }

    /**
     * 最大连接数
     * @param $num
     */
    public function setMaxConnections($num)
    {
        $this->setting['max_conn'] = $num;
    }

    /**
     * Reactor线程投递模式(仅process模式)
     * @param $mode
     * 1，轮循模式，收到会轮循分配给每一个worker进程
     * 2，固定模式，根据连接的文件描述符分配worker。这样可以保证同一个连接发来的数据只会被同一个worker处理
     * 3，抢占模式，主进程会根据Worker的忙闲状态选择投递，只会投递给处于闲置状态的Worker
     * 4，IP分配，根据客户端IP进行取模hash，分配给一个固定的worker进程。
     * 5，UID分配，需要用户代码中调用 $serv-> bind() 将一个连接绑定1个uid。
     * 无状态Server可以使用1或3，同步阻塞Server使用3，异步非阻塞Server使用1
     * 有状态使用2、4、5
     */
    public function setDispatchMode($mode)
    {
        $this->setting['dispatch_mode'] = $mode;
    }

    /**
     * 设置日志和记录级别
     * @param $file
     * @param $level
     */
    public function setLog($file, $level)
    {
        $this->setting['log_file'] = $file;
        $this->setting['log_level'] = $level;
    }

    /**
     * 设置用户和组
     * @param $user
     * @param null $group
     */
    public function setUser($user, $group = null)
    {
        $this->setting['user'] = $user;
        $this->setting['group'] = $group;
    }

    /**
     * 重定向根目录
     * @param $root
     */
    public function setChroot($root)
    {
        $this->setting['chroot'] = $root;
    }


    /**
     * 守护进程模式
     * @param $mode
     */
    public function setDaemonize($mode)
    {
        $this->setting['daemonize'] = $mode;
    }

    /**
     * 存储PID的文件
     * @param $path
     */
    public function setPidFile($path)
    {
        $this->setting['pid_file'] = $path;
    }

    /**
     * 记录慢日志
     * @param $path
     * @param int $timeout
     * @param bool $eventWorker
     */
    public function setSlowLogFile($path, $timeout = 10, $eventWorker = false)
    {
        $this->setting['request_slowlog_file'] = $path;
        $this->setting['request_slowlog_timeout'] = $timeout;
        $this->setting['trace_event_worker'] = $eventWorker;
    }

    /**
     * 设置CPU亲和性
     * @param bool $mode
     * @param array $ignore
     */
    public function setCpuAffinity($mode = true, $ignore = [0])
    {
        $this->setting['open_cpu_affinity'] = $mode;
        $this->setting['cpu_affinity_ignore'] = $ignore;
    }

    /**
     * Reload过程中是否等待Async事件
     * @param bool $mode
     */
    public function setReloadAsync($mode = true)
    {
        $this->setting['reload_async'] = $mode;
    }

    /**
     * 是否投递已关闭连接的事件
     * @param bool $mode
     */
    public function setDiscardTimeoutRequest($mode = true)
    {
        $this->setting['discard_timeout_request'] = $mode;
    }


    /**
     * 设置TaskWorker数量
     * @param $num
     */
    public function setTaskWorkerNum($num)
    {
        $this->setting['task_worker_num'] = $num;
    }

    /**
     * @param $mode
     * 1, 使用unix socket通信，默认模式
     * 2, 使用消息队列通信
     * 3, 使用消息队列通信，并设置为争抢模式
     */
    public function setTaskIPCMode($mode)
    {
        $this->setting['task_ipc_mode'] = $mode;
    }

    /**
     * 设置TaskWorker的最大请求数(重启)
     * @param $num
     */
    public function setTaskMaxRequest($num)
    {
        $this->setting['task_max_request'] = $num;
    }

    /**
     * 设置task的数据临时目录
     * @param $dir
     */
    public function setTaskTmpDir($dir)
    {
        $this->setting['task_tmpdir'] = $dir;
    }

    /**
     * @param $key
     * 设置消息队列的KEY，仅在task_ipc_mode = 2/3时使用。
     * 设置的Key仅作为Task任务队列的KEY，此参数的默认值为ftok($php_script_file, 1)
     */
    public function setTaskMsgQueueKey($key)
    {
        $this->setting['message_queue_key'] = $key;
    }

    /**
     * 设置TCP心跳检查
     * @param $interval
     * @param $idleTIme
     */
    public function setTcpHeartBeatTime($interval, $idleTIme)
    {
        $this->setting['heartbeat_idle_time'] = $idleTIme;
        $this->setting['heartbeat_check_interval'] = $interval;
    }


}
