<?php

namespace Bijou\Swoole;
class MemSerialize
{
    /**
     * @param mixed $data 为要进行序列化的变量，请注意function和resource类型的变量是不支持序列化的
     * @param int $flags 是否启用快速模式，swoole_serialize默认会使用静态表保存关联数组的Key，设置此参数为SWOOLE_FAST_PACK后将不再保存数组key
     * @return mixed 序列化成功后返回二进制字符串，失败返回false
     */
    public static function pack($data, int $flags = 0)
    {
        return \swoole_serialize::pack($data, $flags);
    }

    /**
     * @param string $string 序列化数据，必须是由swoole_serialize::pack函数生成
     * @param mixed $args
     * @return mixed
     */
    public static function unpack($string, $args = null)
    {
        return \swoole_serialize::unpack($string, $args);
    }
}
