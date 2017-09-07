<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/21
 * Time: 22:39
 */

namespace Bijou\Pool;

class OPool
{
    private static $instance;
    /**
     * @var array
     */
    private $map;

    private function __construct()
    {
        $this->map = [];
    }

    /**
     * @return OPool
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new OPool();
        }

        return self::$instance;
    }

    /**
     * @param $name
     * @param array ...$args
     * @return mixed
     */
    public function pop($name, ...$args)
    {
        /**
         * @var \SplStack $pool
         */

        $pool = $this->map[$name];
        if (!$pool) {
            $pool = new \SplStack();
        }

        if ($pool->count()) {
            return $pool->shift();
        } else {
            $reflector = new \ReflectionClass($name);
            if ($args && count($args) > 0) {
                $obj = $reflector->newInstanceArgs($args);
            } else {
                $obj = $reflector->newInstanceWithoutConstructor();
            }

            unset($reflector);
            if(!$this->map[$name]) {
                !$this->map[$name] = $pool;
            }
            return $obj;
        }
    }

    public function push($instance) {
        $name = get_class($instance);
        if($this->map[$name]) {
            $this->map[$name]->push($instance);
        }
    }

}