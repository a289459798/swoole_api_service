<?php

namespace Bijou\Swoole;
class SocketSetting
{
    /**
     * @var \swoole_server
     */
    protected $server;

    /**
     * @var
     */
    protected $setting;

    /**
     * ServerSetting constructor.
     * @param $server
     */
    public function __construct($server = null)
    {
        $this->server = $server;
    }

    /**
     * @return mixed
     */
    public function getSettings()
    {
        return $this->setting;
    }

    /**
     * 设置TCP为包头收包
     * @param $type
     * @param $bodyOffset
     * @param $lengthOffset
     * @param $maxLength
     * c：有符号、1字节
     * C：无符号、1字节
     * s ：有符号、主机字节序、2字节
     * S：无符号、主机字节序、2字节
     * n：无符号、网络字节序、2字节
     * N：无符号、网络字节序、4字节
     * l：有符号、主机字节序、4字节（小写L）
     * L：无符号、主机字节序、4字节（大写L）
     * v：无符号、小端字节序、2字节
     * V：无符号、小端字节序、4字节
     */
    public function setTcpLengthType($type, $bodyOffset = 0, $lengthOffset = 0, $maxLength = 2048 * 1024)
    {
        $this->setting['open_length_check'] = true;
        $this->setting['package_length_type'] = $type;
        $this->setting['package_body_offset'] = $bodyOffset;
        $this->setting['package_length_type'] = $lengthOffset;
        $this->setting['package_max_length'] = $maxLength;
    }

    /**
     * 设置TCP为结束符收包
     * @param $eof
     * @param bool $split
     * @param $maxLength
     */
    public function setTcpEofType($eof, $split = true, $maxLength = 2048 * 1024)
    {
        $this->setting['open_eof_check'] = true;
        $this->setting['package_eof'] = $eof;
        $this->setting['open_eof_split'] = $split;
        $this->setting['package_max_length'] = $maxLength;
    }

    /**
     * TCP发送输出缓存区内存大小
     * @param $size
     */
    public function setTcpBufferOutputSize($size)
    {
        $this->setting['buffer_output_size'] = $size;
    }

    /**
     * TCP客户端连接最大内存占用
     * @param $size
     */
    public function setTcpSocketBufferSize($size)
    {
        $this->setting['socket_buffer_size'] = $size;
    }

    /**
     * TCP端口重用
     * @param bool $mode
     */
    public function setTcpReusePort($mode = true)
    {
        $this->setting['enable_reuse_port'] = $mode;
    }


    /**
     * TCP快打开
     * @param bool $mode
     * 开启TCP快速握手特性。此项特性，可以提升TCP短连接的响应速度，在客户端完成握手的第三步，发送SYN包时携带数据。
     */
    public function setTcpFastOpen($mode = true)
    {
        $this->setting['tcp_fastopen'] = $mode;
    }

    /**
     * TCP延迟接收
     * @param bool $mode
     * 设置此选项为true后，accept客户端连接后将不会自动加入EventLoop，仅触发onConnect回调。
     * worker进程可以调用$serv->confirm($fd)对连接进行确认，此时才会将fd加入EventLoop开始进行数据收发，也可以调用$serv->close($fd)关闭此连接。
     */
    public function setTcpDelayReceive($mode = true)
    {
        $this->setting['enable_delay_receive'] = $mode;
    }

    /**
     * 开启HTTP
     * @param bool $mode
     */
    public function setTcpHttp($mode = true)
    {
        $this->setting['open_http_protocol'] = $mode;
    }

    /**
     * 开启HTTP2
     * @param bool $mode
     */
    public function setTcpHttp2($mode = true)
    {
        $this->setting['open_http2_protocol'] = $mode;
    }

    /**
     * 开启WebSock
     * @param bool $mode
     */
    public function setTcpWebSock($mode = true)
    {
        $this->setting['open_websocket_protocol'] = $mode;
    }

    /**
     * 开启MQTT
     * @param bool $mode
     */
    public function setTcpMqtt($mode = true)
    {
        $this->setting['open_mqtt_protocol'] = $mode;
    }

    /**
     * TCP连接发送数据时会关闭Nagle合并算法，立即发往客户端连接
     * @param bool $mode
     */
    public function setTcpNoDelay($mode = true)
    {
        $this->setting['open_tcp_nodelay'] = $mode;
    }

    /**
     * 设置为一个数值，表示当一个TCP连接有数据发送时才触发accept。
     * @param $second
     * 客户端连接到服务器后不会立即触发accept
     * 在X秒内客户端发送数据，此时会同时顺序触发accept/onConnect/onReceive
     * 在X秒内客户端没有发送任何数据，此时会触发accept/onConnect
     */
    public function setTcpDelayAccept($second)
    {
        $this->setting['tcp_defer_accept'] = $second;
    }

    /**
     * TCP的Listen队列长度
     * @param $num
     */
    public function setTcpBacklog($num)
    {
        $this->setting['backlog'] = $num;
    }

    /**
     * 设置SSL支持
     * @param $cert
     * @param $key
     * @param $method
     * @param $ciphers
     * 文件必须为PEM格式，不支持DER格式，可使用openssl工具进行转换
     * openssl x509 -in cert.crt -outform der -out cert.der
     * openssl x509 -in cert.crt -inform der -outform pem -out cert.pem
     * 支持方法:
     * SWOOLE_SSLv3_METHOD
     * SWOOLE_SSLv3_SERVER_METHOD
     * SWOOLE_SSLv3_CLIENT_METHOD
     * SWOOLE_SSLv23_METHOD（默认加密方法）
     * SWOOLE_SSLv23_SERVER_METHOD
     * SWOOLE_SSLv23_CLIENT_METHOD
     * SWOOLE_TLSv1_METHOD
     * SWOOLE_TLSv1_SERVER_METHOD
     * SWOOLE_TLSv1_CLIENT_METHOD
     * SWOOLE_TLSv1_1_METHOD
     * SWOOLE_TLSv1_1_SERVER_METHOD
     * SWOOLE_TLSv1_1_CLIENT_METHOD
     * SWOOLE_TLSv1_2_METHOD
     * SWOOLE_TLSv1_2_SERVER_METHOD
     * SWOOLE_TLSv1_2_CLIENT_METHOD
     * SWOOLE_DTLSv1_METHOD
     * SWOOLE_DTLSv1_SERVER_METHOD
     * SWOOLE_DTLSv1_CLIENT_METHOD
     */
    public function setSSLConfig($cert, $key, $method, $ciphers = null)
    {
        $this->setting['ssl_cert_file'] = $cert;
        $this->setting['ssl_key_file'] = $key;
        $this->setting['ssl_method'] = $method;
        if ($ciphers) {
            $this->setting['ssl_ciphers'] = $ciphers;
        }
    }

}
