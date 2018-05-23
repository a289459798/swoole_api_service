<?php

namespace Bijou\Swoole;

use Swoole\Table;

class MemTable
{
    /**
     * @var Table
     */
    public $table;

    /**
     * MemTable constructor.
     * @param int $size 数指定表格的最大行数，如果$size不是为2的N次方，如1024、8192,65536等，底层会自动调整为接近的一个数字，如果小于1024则默认成1024，即1024是最小值
     * @param float $conflictProportion 占用的内存总数为 (结构体长度 + KEY长度64字节 + 行尺寸$size) * (1.2预留20%作为hash冲突) * (列尺寸)，如果机器内存不足table会创建失败
     */
    public function __construct($size, $conflictProportion = null)
    {
        $this->table = new Table($size, $conflictProportion);
    }

    /**
     * 内存表增加一列
     * swoole_table::TYPE_INT默认为4个字节，可以设置1，2，4，8一共4种长度
     * swoole_table::TYPE_STRING设置后，设置的字符串不能超过此长度
     * swoole_table::TYPE_FLOAT会占用8个字节的内存
     * @param  string $name 指定字段的名称
     * @param string $type 指定字段类型，支持3种类型，swoole_table::TYPE_INT, swoole_table::TYPE_FLOAT, swoole_table::TYPE_STRING
     * @param int $size 指定字符串字段的最大长度，单位为字节
     * @return mixed
     */
    public function column($name, $type, $size = null)
    {
        return $this->table->column($name, $type, $size);
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return $this->table->create();
    }

    /**
     * 获取一行数据
     * @param string $key
     * @param string $field
     * @return mixed
     */
    public function get(string $key, $field = null)
    {
        return $this->table->get($key, $field);
    }

    /**
     * 设置行的数据，swoole_table使用key-value的方式来访问数据
     * @param string $k 数据的key，相同的$key对应同一行数据，如果set同一个key，会覆盖上一次的数据
     * @param array $v 必须与字段定义的$name完全相同
     * @return mixed
     */
    public function set(string $k, array $v)
    {
        return $this->table->set($k, $v);
    }

    /**
     * 原子自增操作
     * @param string $key 指定数据的key，如果$key对应的行不存在，默认列的值为0
     * @param string $column 指定列名，仅支持浮点型和整型字
     * @param int $incrby 增量，默认为1。如果列为整形，$incrby必须为int型，如果列为浮点型，$incrby必须为float类型
     * @return mixed
     */
    public function incr(string $key, string $column, $incrby = 1)
    {
        return $this->table->incr($key, $column, $incrby);
    }

    /**
     * @param string $key 指定数据的key，如果$key对应的行不存在，默认列的值为0
     * @param string $column 指定列名，仅支持浮点型和整型字段
     * @param int $decrby 减量，默认为1。如果列为整形，$decrby必须为int型，如果列为浮点型，$decrby必须为float类型
     * @return mixed
     */
    public function decr($key, $column, $decrby = 1)
    {
        return $this->table->decr($key, $column, $decrby);
    }

    /**
     * 检查table中是否存在某一个key
     * @param $key
     * @return mixed
     */
    public function exist($key)
    {
        return $this->table->exist($key);
    }

    /**
     * 删除数据
     * @param $key
     * @return mixed
     * 对应的数据不存在，将返回false
     * 成功删除返回true
     */
    public function del($key)
    {
        return $this->table->del($key);
    }
}
